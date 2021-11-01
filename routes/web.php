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

Route::get('/', function () {
    return view('app');
});

Route::get('/login', function() {return view('app');})->middleware('guest');
Route::get('/register', function() {return view('app');});
Route::get('/logout', function() {return redirect('/login');});

Route::post('/login', [\App\Http\Controllers\Auth\AuthenticationController::class, 'authenticate']);
Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticationController::class, 'revokeAuthentication']);
// User registration is handled by API
