<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\DailyUpdateController;
use App\Http\Controllers\Api\MockupController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:10,1');

Route::get('/portfolio', [ProjectController::class, 'portfolio']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/dashboard/stats', [ProjectController::class, 'stats']);

    Route::apiResource('projects', ProjectController::class);

    Route::get('/mockups', [MockupController::class, 'all']);
    Route::get('/projects/{project}/mockups', [MockupController::class, 'index']);
    Route::post('/mockups', [MockupController::class, 'store']);
    Route::delete('/mockups/{mockup}', [MockupController::class, 'destroy']);

    Route::get('/daily-updates', [DailyUpdateController::class, 'index']);
    Route::post('/daily-updates', [DailyUpdateController::class, 'store']);
    Route::delete('/daily-updates/{dailyUpdate}', [DailyUpdateController::class, 'destroy']);

    Route::get('/contacts', [ContactController::class, 'index']);
    Route::patch('/contacts/{contact}/read', [ContactController::class, 'markRead']);
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy']);

    Route::apiResource('clients', ClientController::class);

    Route::middleware('super_admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });
});
