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
Route::post('/login', [\App\Http\Controllers\Auth\AuthenticationController::class, 'authenticate'])->name('password.reset');
Route::post('/login/two-factor', [\App\Http\Controllers\Auth\TwoFactorVerificationController::class, 'verify']);
Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticationController::class, 'revokeAuthentication']);
// User registration is handled by API

// Password recovery
Route::post('/forgot', [\App\Http\Controllers\Recovery\PasswordController::class, 'forgot']);
Route::post('/reset', [\App\Http\Controllers\Recovery\PasswordController::class, 'reset']);
