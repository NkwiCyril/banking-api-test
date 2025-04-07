<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\BankAccountController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('api_key')->group(function () {
    
    // Public auth routes
    Route::post('login', [AuthController::class, 'login']);

    // Authenticated user routes
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('logout', [AuthController::class, 'logout']);

        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('accounts', BankAccountController::class)->only(['store', 'index', 'show']);

        Route::post('transactions/transfer', [TransactionController::class, 'transfer']);
        Route::get('accounts/{account}/balance', [BankAccountController::class, 'balance']);
        Route::get('accounts/{account}/transactions', [TransactionController::class, 'history']);
    });
});
