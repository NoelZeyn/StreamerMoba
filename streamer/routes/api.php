<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\HeroController;
use App\Http\Controllers\MatchController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\PublicQueueController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaweriaController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\StreamerQueueController;
use App\Http\Controllers\TransactionController;
use Illuminate\Container\Attributes\Auth;

Route::get('/captcha', [AuthController::class, 'captcha']);
Route::prefix('v1')->group(function () {
    Route::get('/public/queue-list', [PublicQueueController::class, 'getQueueItems'])->middleware('throttle:60,1');
    Route::get('/mlbb-nickname', [SaweriaController::class, 'proxyNickname'])->middleware('throttle:20,1');
    Route::post('/webhook/saweria/{token}', [SaweriaController::class, 'handle']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/public/join-queue', [QueueController::class, 'store'])->middleware('throttle:5,1');
    Route::get('/public/streamers', [PublicQueueController::class, 'getStreamers'])->middleware('throttle:30,1');
    Route::get('/public/schedules', [PublicQueueController::class, 'getSchedules'])->middleware('throttle:30,1');

    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::get('/queues/{schedule_id}', [StreamerQueueController::class, 'show']);
        Route::get('/queues2', [StreamerQueueController::class, 'index2']);
        Route::patch('/queues/{id}/status', [StreamerQueueController::class, 'updateStatus']);
        Route::get('/vip/search', [StreamerQueueController::class, 'searchVip']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('/matches', [MatchController::class, 'store']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('/transactions', [TransactionController::class, 'store']);
        Route::get('/transactions', [TransactionController::class, 'index']);
        Route::get('/transactions/{id}', [TransactionController::class, 'show']);
        Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);
    });
    Route::middleware('auth:api')->group(function () {
        Route::get('/donations', [DonationController::class, 'index']);
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

    Route::middleware('auth:api')->group(function () {
        Route::post('/players', [PlayerController::class, 'store']);
        Route::get('/players', [PlayerController::class, 'index']);
        Route::get('/players/{id}', [PlayerController::class, 'show']);
        Route::put('/players/{id}', [PlayerController::class, 'update']);
        Route::delete('/players/{id}', [PlayerController::class, 'destroy']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('/seasons', [SeasonController::class, 'store']);
        Route::get('/seasons', [SeasonController::class, 'index']);
        Route::get('/seasons/{id}', [SeasonController::class, 'show']);
        Route::put('/seasons/{id}', [SeasonController::class, 'update']);
        Route::delete('/seasons/{id}', [SeasonController::class, 'destroy']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('/heroes', [HeroController::class, 'store']);
        Route::get('/heroes', [HeroController::class, 'index']);
        Route::get('/heroes/{id}', [HeroController::class, 'show']);
        Route::put('/heroes/{id}', [HeroController::class, 'update']);
        Route::delete('/heroes/{id}', [HeroController::class, 'destroy']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('/roles', [RoleController::class, 'store']);
        Route::get('/roles', [RoleController::class, 'index']);
        Route::get('/roles/{id}', [RoleController::class, 'show']);
        Route::put('/roles/{id}', [RoleController::class, 'update']);
        Route::delete('/roles/{id}', [RoleController::class, 'destroy']);
    });
});
