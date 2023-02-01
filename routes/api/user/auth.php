<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Auth API Routes
|--------------------------------------------------------------------------
|
| Endpoint: /api/user/auth
|
*/

/**
 * Two-Factor Authentication
 */
Route::prefix('two-factor-authentication')->group(function () {
    /*
    * Get 2FA setup data
    */
    Route::get('/', [\App\Http\Controllers\Auth\TwoFactorController::class, 'get']);
    /*
    * Enable 2FA
    */
    Route::post('/', [\App\Http\Controllers\Auth\TwoFactorController::class, 'enable']);
    /*
    * Disable 2FA
    */
    Route::delete('/', [\App\Http\Controllers\Auth\TwoFactorController::class, 'disable']);
});

/**
 * Logout
 */
Route::prefix('logout')->group(function () {
    Route::post('/', [\App\Http\Controllers\Auth\AuthenticationController::class, 'logout']);
    Route::post('/all', [\App\Http\Controllers\Auth\AuthenticationController::class, 'logoutAll']);
});

/**
 * Verify authorization token
 */
Route::post('verify-token', [\App\Http\Controllers\Auth\AuthorizationController::class, 'verifyToken']);
