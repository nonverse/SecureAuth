<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
 * Verify a pre-login email request from client
 */
Route::post('initialize-email', [\App\Http\Controllers\User\UserController::class, 'email']);
Route::get('user-cookie', [\App\Http\Controllers\User\UserController::class, 'cookie']);
Route::delete('user-cookie', [\App\Http\Controllers\User\UserController::class, 'clearCookie']);
