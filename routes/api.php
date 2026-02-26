<?php

use App\Http\Controllers\Api\Flutter\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Flutter Mobile App
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api automatically by Laravel.
| Flutter routes are additionally prefixed with /flutter.
|
| Base URL:  /api/flutter/...
|
*/

Route::prefix('flutter')->group(function () {

    // ── Auth ────────────────────────────────────────────────────────
    Route::prefix('auth')->group(function () {

        // Public — no JWT required
        Route::middleware('throttle:otp')->group(function () {
            Route::post('send-otp',   [AuthController::class, 'sendOtp']);
            Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
        });

        // Protected — valid JWT required
        Route::middleware('jwt.auth')->group(function () {
            Route::get('me',        [AuthController::class, 'me']);
            Route::post('refresh',  [AuthController::class, 'refresh']);
            Route::post('logout',   [AuthController::class, 'logout']);
        });
    });

    // ── Future module routes go below ──────────────────────────────
    // Route::middleware('jwt.auth')->group(function () {
    //     Route::apiResource('profile',  ProfileController::class);
    //     Route::apiResource('bookings', BookingController::class);
    //     Route::apiResource('services', ServiceController::class);
    //     ...
    // });
});
