<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MatchController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;
use Illuminate\Container\Attributes\Auth;

Route::prefix('v1')->middleware('auth:api')->group(function () {
    Route::post('/matches', [MatchController::class, 'store']);
    Route::get('/players', [PlayerController::class, 'index']);
    Route::post('/players', [PlayerController::class, 'store']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});