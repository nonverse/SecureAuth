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



Route::group(['middleware' => 'guest'], function() {
    Route::get('/', function () {return view('app');});
    Route::get('/login', function() {return view('app');});
    Route::get('/register', function() {return view('app');});
    Route::get('/logout', function() {return abort(404);});

    Route::get('/forgot', function() {return view('app');});
    Route::get('/reset', function() {return view('app');});
});
