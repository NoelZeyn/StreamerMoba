<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MatchController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;
use Illuminate\Container\Attributes\Auth;

Route::get('/captcha', [AuthController::class,'captcha']);
Route::prefix('v1')->group(function () {

    // auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // protected
    Route::middleware('auth:api')->group(function () {
        Route::post('/matches', [MatchController::class, 'store']);
        Route::get('/players', [PlayerController::class, 'index']);
        Route::post('/players', [PlayerController::class, 'store']);
    });
});
