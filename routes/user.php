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

Route::get('/two-factor-authentication', [\App\Http\Controllers\Auth\TwoFactorController::class, 'setup']);
