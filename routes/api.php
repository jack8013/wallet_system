<?php

use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

Route::get('/wallets', [WalletController::class, 'index']);
Route::get('/wallets/{id}', [WalletController::class, 'show']);
Route::post('/wallets', [WalletController::class, 'store']);
Route::put('/wallets/{id}', [WalletController::class, 'update']);
Route::delete('/wallets/{id}', [WalletController::class, 'destroy']);
