<?php

use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

Route::get('/wallets', [WalletController::class, 'index']);
Route::get('/wallets/{id}', [WalletController::class, 'show']);
Route::post('/wallets', [WalletController::class, 'store']);
Route::post('/wallets/{id}/deposit', [WalletController::class, 'deposit']);
Route::post('/wallets/{id}/withdraw', [WalletController::class, 'withdraw']);
Route::delete('/wallets/{id}', [WalletController::class, 'destroy']);
Route::get('/wallets/{id}/transactions', [WalletController::class, 'showTransactions']);
