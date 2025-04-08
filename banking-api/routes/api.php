<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BankAccountController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'api_key'])->group(function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('role:admin,employee')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('accounts', BankAccountController::class)->only(['store']);
        Route::post('transactions/transfer', [TransactionController::class, 'transfer']);
    });

    // Accessible to any authenticated user
    Route::get('accounts', [BankAccountController::class, 'index']);
    Route::get('accounts/{account}', [BankAccountController::class, 'show']);
    Route::get('accounts/{account}/balance', [BankAccountController::class, 'balance']);
    Route::get('accounts/{account}/transactions', [TransactionController::class, 'history']);
});
