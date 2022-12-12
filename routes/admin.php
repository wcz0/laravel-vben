<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AdminController::class, 'login']);


Route::middleware('auth:admin')->group(function () {
    // Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AdminController::class, 'logout']);
    Route::post('refresh', [AdminController::class, 'refresh']);

    Route::get('getUserInfo', [AdminController::class, 'admin']);
    Route::get('getMenu', [AdminController::class, 'menu']);
});

Route::group(['middleware' => [
    'http_request',
    'auth:admin',
    ]], function () {

});

