<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CORS protected stateful API routes
| These routes can only be access by apps on the .nonverse.net domain
|--------------------------------------------------------------------------
| Endpoint: /api
|
*/
Route::post('/verify-user-email', [\App\Http\Controllers\Auth\AuthenticationController::class, 'verifyUserEmail']);

