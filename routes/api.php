<?php

use App\Http\Controllers\Api\Users\RegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes.
|--------------------------------------------------------------------------
*/

Route::middleware(['throttle:register-user'])->group(function () {
    Route::post('/register', [RegisterController::class, 'register']);
});
