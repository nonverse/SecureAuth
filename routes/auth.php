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

Route::post('/login', [\App\Http\Controllers\Auth\AuthenticationController::class, 'authenticate']);
Route::post('/login/two-factor', [\App\Http\Controllers\Auth\TwoFactorVerificationController::class, 'verify']);
Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticationController::class, 'revokeAuthentication']);
// User registration is handled by API
