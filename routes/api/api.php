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
Route::prefix('user')->group(base_path('routes/api/user.php'));
