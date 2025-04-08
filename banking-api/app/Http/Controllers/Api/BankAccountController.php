<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BankAccountResource;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankAccountController extends Controller
{
    protected $relationships = [
        'customer',
        'sentTransactions',
        'receivedTransactions',
    ];

    public function index()
    {
        $bankAccounts = BankAccount::with($this->relationships)->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Accounts retrieved successfully.',
            'statusCode' => 200,
            'data' => BankAccountResource::collection($bankAccounts),
        ], 200);
    }

    public function show(int $id)
    {
        $account = BankAccount::findOrFail($id);

        // Check if the account exists
        if (! $account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Account retrieved successfully.',
            'statusCode' => 200,
            'data' => new BankAccountResource($account->load($this->relationships)),
        ], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'initial_deposit' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $account = BankAccount::create([
                'customer_id' => $data['customer_id'],
                'account_number' => 'ACCT-'.strtoupper(uniqid()),
                'balance' => $data['initial_deposit'],
            ]);

            if ($data['initial_deposit'] > 0) {
                Transaction::create([
                    'sender_account_id' => null,
                    'receiver_account_id' => $account->id,
                    'amount' => $data['initial_deposit'],
                    'type' => 'deposit',
                    'status' => 'completed',
                    'description' => 'Initial deposit',
                    'created_by' => $request->user()->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Account created successfully!',
                'statusCode' => 201,
                'data' => new BankAccountResource($account),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Bank account creation failed', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Could not create account. Please try again.'], 500);
        }
    }

    public function balance(int $id)
    {
        $account = BankAccount::findOrFail($id);

        // Check if the account exists
        if (! $account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Account balance retrieved successfully.',
            'statusCode' => 200,
            'data' => [
                'account_id' => $account->id,
                'account_number' => $account->account_number,
                'balance' => $account->balance,
            ],
        ], 200);
    }
}
