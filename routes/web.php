<?php

use App\Http\Controllers\Users\IndexController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes.
|--------------------------------------------------------------------------
*/

Route::get('/users', [IndexController::class, 'all']);
