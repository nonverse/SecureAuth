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
Route::get('authorize', [\App\Http\Controllers\OAuth\AuthorizationController::class, 'authorize']);
