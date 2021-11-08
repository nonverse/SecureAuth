<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CORS and auth protected user API routes
| These routes can only be access by apps on the .nonverse.net domain by
| a session authenticated user
|--------------------------------------------------------------------------
| Endpoint: /api/user
|
*/

// No auth routes
Route::post('/', [\App\Http\Controllers\UserController::class, 'verify'])->withoutMiddleware('auth');
Route::get('/cookie', [\App\Http\Controllers\UserController::class, 'cookie'])->withoutMiddleware('auth');

// Get current user
Route::get('/', [\App\Http\Controllers\UserController::class, 'get']);

// 2FA routes
Route::get('/two-factor-authentication', [\App\Http\Controllers\Auth\TwoFactorController::class, 'setup']);
Route::post('/two-factor-authentication', [\App\Http\Controllers\Auth\TwoFactorController::class, 'enable']);
Route::delete('/two-factor-authentication', [\App\Http\Controllers\Auth\TwoFactorController::class, 'disable']);
