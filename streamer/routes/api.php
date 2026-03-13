<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MatchController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\SaweriaController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TransactionController;
use Illuminate\Container\Attributes\Auth;

Route::get('/captcha', [AuthController::class, 'captcha']);
Route::prefix('v1')->group(function () {
    Route::post('/webhook/saweria/{token}', [SaweriaController::class, 'handle']);
    

    // auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // protected
    Route::middleware('auth:api')->group(function () {
        Route::post('/matches', [MatchController::class, 'store']);
        Route::get('/players', [PlayerController::class, 'index']);
        Route::post('/players', [PlayerController::class, 'store']);
        Route::get('/players/{id}', [PlayerController::class, 'show']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('/transactions', [TransactionController::class, 'store']);
        Route::get('/transactions', [TransactionController::class, 'index']);
        Route::get('/transactions/{id}', [TransactionController::class, 'show']);
        Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('/schedules', [ScheduleController::class, 'store']);
        Route::get('/schedules', [ScheduleController::class, 'index']);
        Route::get('/schedules/{id}', [ScheduleController::class, 'show']);
        Route::put('/schedules/{id}', [ScheduleController::class, 'update']);
        Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy']);
        Route::post('/schedules/{id}/finish', [ScheduleController::class, 'finish']);
        Route::post('/schedules/{id}/cancel', [ScheduleController::class, 'cancel']);
        Route::post('/schedules/{id}/start', [ScheduleController::class, 'start']); 
        Route::post('/schedules/{id}/reopen', [ScheduleController::class, 'reopen']);
    });
});
