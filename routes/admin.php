<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'adminLogin']);


Route::middleware('auth:admin')->group(function () {
    // Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'adminRefresh']);

    Route::get('getUserInfo', [AdminController::class, 'admin']);
    Route::get('getMenu', [AdminController::class, 'menu']);
});

Route::group(['middleware' => [
    'http_request',
    'auth:admin',
    ]], function () {

});

