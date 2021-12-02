<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
| Endpoint: /*
|
*/

// Basic authentication
Route::post('/login', [\App\Http\Controllers\Auth\AuthenticationController::class, 'authenticate'])->name('auth.login');
Route::post('/login/verify-email', [\App\Http\Controllers\Auth\AuthenticationController::class, 'verifyEmail'])->name('auth.verify');
Route::post('/login/two-factor', [\App\Http\Controllers\Auth\TwoFactorVerificationController::class, 'verify'])->name('auth.2fa');
Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticationController::class, 'revokeAuthentication'])->name('auth.logout');
Route::post('/logout/everywhere', [\App\Http\Controllers\Auth\AuthenticationController::class, 'revokeAllAuthentication'])->name('auth.logout-all');

// Password recovery
Route::post('/forgot', [\App\Http\Controllers\Recovery\PasswordController::class, 'forgot'])->name('password.forgot');
Route::post('/reset', [\App\Http\Controllers\Recovery\PasswordController::class, 'reset'])->name('password.reset');
