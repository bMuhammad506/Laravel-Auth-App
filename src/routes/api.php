<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerifyController;
use App\Http\Middleware\AuthenticateWithToken;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

Route::post('/password/reset', [AuthController::class, 'sendPasswordResetOtp'])->middleware('throttle:password-reset-otp');
Route::post('/password/reset/confirm', [AuthController::class, 'resetPassword']);


Route::middleware(AuthenticateWithToken::class)->group(function () {
    Route::get('/check-token', [VerifyController::class, 'check']);
});
