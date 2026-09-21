<?php

use App\Http\Controllers\Merchant\Auth\LoginController;
use App\Http\Controllers\Merchant\MerchantDashboardController;
use App\Http\Controllers\Merchant\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::prefix('merchant')->middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('merchant.register');
    Route::post('register', [RegisteredUserController::class, 'store'])->name('merchant.store');
    Route::get('login', [LoginController::class, 'create'])->name('merchant.login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::prefix('merchant')->middleware('auth:merchant')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('merchant.logout');

    // Dashboard
    Route::get('/dashboard', [MerchantDashboardController::class, 'index'])->name('merchant.dashboard');
    
});