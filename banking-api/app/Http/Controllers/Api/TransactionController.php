<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function transfer(Request $request)
    {
        $data = $request->validate([
            'sender_account_number' => 'required|exists:bank_accounts,account_number',
            'receiver_account_number' => 'required|exists:bank_accounts,account_number|different:sender_account_number',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        try {
            $sender = BankAccount::where('account_number', $data['sender_account_number'])
                ->lockForUpdate()
                ->firstOrFail();

            $receiver = BankAccount::where('account_number', $data['receiver_account_number'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($sender->balance < $data['amount']) {
                return response()->json(['message' => 'Insufficient funds'], 400);
            }

            DB::beginTransaction();

            $sender->decrement('balance', $data['amount']);
            $receiver->increment('balance', $data['amount']);

            $transaction = Transaction::create([
                'sender_account_id' => $sender->id,
                'receiver_account_id' => $receiver->id,
                'amount' => $data['amount'],
                'type' => 'transfer',
                'status' => 'completed',
                'description' => $data['description'] ?? null,
                'created_by' => $request->user()->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transfer completed successfully.',
                'statusCode' => 200,
                'data' => [
                    'sender_account' => $sender->fresh(),
                    'receiver_account' => $receiver->fresh(),
                    'transaction' => new TransactionResource($transaction),
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Transfer failed', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'An error occurred while processing the transfer.'], 500);
        }
    }

    public function history(int $id)
    {
        $account = BankAccount::findOrFail($id);

        if (! $account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        $transactions = Transaction::where('sender_account_id', $account->id)
            ->orWhere('receiver_account_id', $account->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Transaction history retrieved successfully.',
            'statusCode' => 200,
            'data' => TransactionResource::collection($transactions),
        ], 200);
    }
}
