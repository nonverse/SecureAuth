<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
 * Base email request
 */
Route::view('/', 'app')->middleware('nousercookie');

/*
 * Login
 */
Route::prefix('login')->group(function() {
    Route::view('/', 'app')->middleware('usercookie')->name('login');
    Route::post('/', [\App\Http\Controllers\Auth\AuthenticationController::class, 'login']);
});

/*
 * Registration
 */
Route::prefix('register')->group(function() {
    Route::view('/', 'app');
    Route::post('/', [\App\Http\Controllers\User\UserController::class, 'store']);
});
