<?php

use App\Http\Controllers\CommissionPaymentCallbackController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\PublicStorageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/storage/{path}', [PublicStorageController::class, 'show'])
    ->where('path', '.*')
    ->name('storage.public');

Route::redirect('/login', '/admin/login')->name('login');

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

Route::post('/payments/phonepe/callback', [CommissionPaymentCallbackController::class, 'phonePeCallback']);
