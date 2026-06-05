<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DriverRideController;
use App\Http\Controllers\Api\PlaceSearchController;
use App\Http\Controllers\Api\RideController;
use App\Support\VehicleTypes;
use Illuminate\Support\Facades\Route;

Route::get('/vehicle-types', fn () => response()->json(['data' => VehicleTypes::forApi()]));

Route::post('/register', [AuthController::class, 'register']);
Route::post('/register/driver', [AuthController::class, 'registerDriver']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/places/config', [PlaceSearchController::class, 'config']);
    Route::get('/places/search', [PlaceSearchController::class, 'search'])->middleware('throttle:30,1');
    Route::get('/places/reverse', [PlaceSearchController::class, 'reverse'])->middleware('throttle:20,1');

    Route::get('/rides', [RideController::class, 'index']);
    Route::post('/rides/driver-options', [RideController::class, 'driverOptions'])->middleware('throttle:30,1');
    Route::post('/rides', [RideController::class, 'store']);
    Route::get('/rides/{id}', [RideController::class, 'show']);
    Route::post('/rides/{ride}/cancel', [RideController::class, 'cancel']);
    Route::post('/rides/{ride}/passenger-location', [RideController::class, 'updatePassengerLocation'])->middleware('throttle:60,1');

    Route::get('/driver/me', [DriverRideController::class, 'me']);
    Route::post('/driver/location', [DriverRideController::class, 'updateLocation'])->middleware('throttle:120,1');
    Route::post('/driver/profile', [DriverRideController::class, 'updateProfile'])->middleware('throttle:30,1');
    Route::get('/driver/rides', [DriverRideController::class, 'index']);
    Route::post('/driver/rides/{ride}/accept', [DriverRideController::class, 'acceptRide']);
    Route::post('/driver/rides/{ride}/reject', [DriverRideController::class, 'rejectRide']);
    Route::post('/driver/rides/{ride}/complete', [DriverRideController::class, 'completeRide']);
});
