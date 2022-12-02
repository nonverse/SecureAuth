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
Route::view('/', 'app')->middleware(['guest', 'nousercookie']);

/*
 * Recovery
 */
Route::prefix('recovery')->middleware(['guest'])->group(function () {
    /*
     * Password recovery
     */
    Route::view('/password', 'app')->name('password.reset');
    Route::post('/password', [\App\Http\Controllers\Recovery\PasswordRecoveryController::class, 'forgot']);
    Route::post('/password/reset', [\App\Http\Controllers\Recovery\PasswordRecoveryController::class, 'reset']);
    /*
     * 2FA recovery
     */
    Route::view('/two-factor', 'app');
});

/*
 * Login
 */
Route::prefix('login')->group(function () {
    Route::view('/', 'app')->middleware(['usercookie', 'guest'])->name('login');
    Route::post('/', [\App\Http\Controllers\Auth\AuthenticationController::class, 'login']);
    Route::post('/two-factor', [\App\Http\Controllers\Auth\TwoFactorController::class, 'verify']);
});

/*
 * Authorize Action
 */
Route::prefix('authorize')->middleware(['auth'])->group(function () {
    Route::view('/', 'app');
    Route::post('/', [\App\Http\Controllers\Auth\AuthorizationController::class, 'authorizationToken']);
});

/*
 * Logout
 */
Route::prefix('logout')->group(function () {
    Route::post('/', [\App\Http\Controllers\Auth\AuthenticationController::class, 'logout']);
    Route::post('/all', [\App\Http\Controllers\Auth\AuthenticationController::class, 'logoutAll']);
});

/*
 * Registration
 */
Route::prefix('register')->group(function () {
    Route::view('/', 'app')->middleware('guest');
    Route::post('/', [\App\Http\Controllers\User\UserController::class, 'store']);
});
