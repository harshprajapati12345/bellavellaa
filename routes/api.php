<?php

use App\Http\Controllers\Api\Flutter\AuthController as FlutterAuthController;
use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api automatically by Laravel.
|
| /api/flutter/*  → Customer mobile app (JWT via OTP)
| /api/admin/*    → Admin panel API     (JWT via email+password)
|
*/

// ═══════════════════════════════════════════════════════════════════
// FLUTTER — Customer Mobile App
// ═══════════════════════════════════════════════════════════════════

Route::prefix('flutter')->group(function () {

    Route::prefix('auth')->group(function () {

        // Public — no JWT required
        Route::middleware('throttle:otp')->group(function () {
            Route::post('send-otp', [FlutterAuthController::class, 'sendOtp']);
            Route::post('verify-otp', [FlutterAuthController::class, 'verifyOtp']);
        });

        // Protected — valid customer JWT required
        Route::middleware('jwt.auth')->group(function () {
            Route::get('me', [FlutterAuthController::class, 'me']);
            Route::post('refresh', [FlutterAuthController::class, 'refresh']);
            Route::post('logout', [FlutterAuthController::class, 'logout']);
        });
    });

    // Future customer routes ──────────────────────────────────────
    // Route::middleware('jwt.auth')->group(function () {
    //     Route::apiResource('profile',  ProfileController::class);
    //     Route::apiResource('bookings', BookingController::class);
    // });
});

// ═══════════════════════════════════════════════════════════════════
// ADMIN — Admin Panel API
// ═══════════════════════════════════════════════════════════════════

Route::prefix('admin')->group(function () {

    Route::prefix('auth')->group(function () {

        // Public — no JWT required
        Route::post('login', [AdminAuthController::class, 'login']);

        // Protected — valid admin JWT required
        Route::middleware('jwt.admin')->group(function () {
            Route::get('me', [AdminAuthController::class, 'me']);
            Route::post('refresh', [AdminAuthController::class, 'refresh']);
            Route::post('logout', [AdminAuthController::class, 'logout']);
        });
    });

    // Future admin routes ─────────────────────────────────────────
    Route::middleware('jwt.admin')->group(function () {
        Route::apiResource('customers', \App\Http\Controllers\Api\Admin\CustomerController::class);
        Route::apiResource('categories', \App\Http\Controllers\Api\Admin\CategoryController::class);
    });
});
