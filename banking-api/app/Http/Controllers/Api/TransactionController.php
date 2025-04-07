<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function transfer(Request $request)
    {
        $data = $request->validate([
            'sender_account_id' => 'required|exists:bank_accounts,id',
            'receiver_account_id' => 'required|exists:bank_accounts,id|different:sender_account_id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        try {
            $sender = BankAccount::lockForUpdate()->findOrFail($data['sender_account_id']);
            $receiver = BankAccount::lockForUpdate()->findOrFail($data['receiver_account_id']);

            if ($sender->balance < $data['amount']) {
                return response()->json(['message' => 'Insufficient funds'], 400);
            }

            DB::beginTransaction();

            $sender->decrement('balance', $data['amount']);
            $receiver->increment('balance', $data['amount']);

            Transaction::create([
                'sender_account_id' => $sender->id,
                'receiver_account_id' => $receiver->id,
                'amount' => $data['amount'],
                'type' => 'transfer',
                'status' => 'completed',
                'description' => $data['description'] ?? null,
                'created_by' => $request->user()->id,
            ]);

            DB::commit();

            return response()->json(['message' => 'Transfer successful'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Transfer failed', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'An error occurred while processing the transfer.'], 500);
        }
    }

    public function history(BankAccount $account)
    {
        $transactions = Transaction::where('sender_account_id', $account->id)
            ->orWhere('receiver_account_id', $account->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($transactions);
    }
}
