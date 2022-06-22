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
Route::view('/login', 'app')->middleware('usercookie');

/*
 * Register
 */
Route::view('/register', 'app');
Route::post('/register', [\App\Http\Controllers\User\UserController::class, 'store']);
