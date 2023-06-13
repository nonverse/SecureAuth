<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
|
| Endpoint: /api/user
|
*/

// User recovery data routes
Route::prefix('recovery')->group(function () {
    Route::get('/', [\App\Http\Controllers\User\RecoveryController::class, 'get']);
});
