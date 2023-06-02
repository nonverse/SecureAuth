<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| OAuth2 API Routes
|--------------------------------------------------------------------------
|
| Endpoint: /api/oauth
|
*/

// Validate a client
Route::post('validate-client', [\App\Http\Controllers\OAuth2\AuthorizationController::class, 'show']);
