<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Endpoint: /api
|
*/

/*
 * User API routes
 */
Route::prefix('user')->group(base_path('routes/api/user.php'));

/*
 * API validation routes
 */
Route::prefix('validator')->group(base_path('routes/api/validator.php'));
