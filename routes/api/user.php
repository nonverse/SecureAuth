<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
|
| Endpoint: /api/user
|
*/

/*
 * Get the currently logged in user
 */
Route::get('/', [\App\Http\Controllers\User\UserController::class, 'get'])->middleware('auth');
/*
 * Initialize a user's email
 */
Route::post('initialize', [\App\Http\Controllers\User\UserController::class, 'email']);
/*
 * Get the decrypted value of user cookie
 */
Route::get('cookie', [\App\Http\Controllers\User\UserController::class, 'getCookie']);
/*
 * Clear user cookie
 */
Route::delete('cookie', [\App\Http\Controllers\User\UserController::class, 'clearCookie']);

/**
 * Two-Factor Authentication
 */
Route::prefix('two-factor-authentication')->middleware('auth')->group(function() {
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
