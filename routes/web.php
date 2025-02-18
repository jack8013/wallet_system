<?php

use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [WalletController::class, 'index']);
Route::post('/create', [WalletController::class, 'store'])->name('store');
Route::get('/wallet/{id}', [WalletController::class, 'details'])->name('details');
Route::post('/wallet/{id}/deposit', [WalletController::class, 'deposit'])->name('deposit');
Route::post('/wallet/{id}/withdraw', [WalletController::class, 'withdraw'])->name('withdraw');
Route::delete('/wallet/{id}', [WalletController::class, 'destroy'])->name('delete');
