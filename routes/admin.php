<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AdminController::class, 'login']);


Route::middleware('auth:admin')->group(function () {
    // Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AdminController::class, 'logout']);
    Route::post('refresh', [AdminController::class, 'refresh']);

    Route::get('get-user-info', [AdminController::class, 'admin']);
    Route::get('get-menu', [AdminController::class, 'menu']);
});

Route::group(['middleware' => [
    'casbin',
    'auth:admin',
    ]], function () {
    Route::get('/dashboard', [AdminController::class, 'adminList']);
    Route::get('/system/role/index', [SystemController::class, 'index']);
    Route::put('/system/role/update', [SystemController::class, 'update']);
    Route::post('/system/role/create', [SystemController::class, 'create']);
    Route::delete('/system/role/delete', [SystemController::class, 'delete']);
});

