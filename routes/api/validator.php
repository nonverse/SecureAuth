<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
|
| Endpoint: /api/validator
|
*/

/*
 * Validate an activation key
 */
Route::post('/activation-key', [\App\Http\Controllers\Api\ApiValidationController::class, 'activationKey']);
