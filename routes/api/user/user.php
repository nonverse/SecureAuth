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
