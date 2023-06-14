<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
|
| Endpoint: /user
|
*/

/*
 * Get the currently logged in user
 */
Route::get('/', [\App\Http\Controllers\UserController::class, 'get']);
/*
 * Initialize a user's email
 */
Route::post('initialize', [\App\Http\Controllers\UserController::class, 'email']);
/*
 * Get the decrypted value of user cookie
 */
Route::get('cookie', [\App\Http\Controllers\UserController::class, 'getCookie']);
/*
 * Clear user cookie
 */
Route::delete('cookie', [\App\Http\Controllers\UserController::class, 'clearCookie']);
