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
        Route::apiResource('packages',  \App\Http\Controllers\Api\Admin\PackageController::class);
        Route::apiResource('services',  \App\Http\Controllers\Api\Admin\ServiceController::class);
        Route::apiResource('offers',    \App\Http\Controllers\Api\Admin\OfferController::class);
        Route::apiResource('reviews',   \App\Http\Controllers\Api\Admin\ReviewController::class)->except(['store']);
        
        // Settings - Bulk update pattern
        Route::get('settings', [App\Http\Controllers\Api\Admin\SettingController::class, 'index']);
        Route::get('settings/{key}', [App\Http\Controllers\Api\Admin\SettingController::class, 'show']);
        Route::post('settings', [App\Http\Controllers\Api\Admin\SettingController::class, 'update']);
        
        // Assignments
        Route::get('assignments', [App\Http\Controllers\Api\Admin\AssignmentController::class, 'index']);
        Route::post('assignments', [App\Http\Controllers\Api\Admin\AssignmentController::class, 'store']);
        
        // CRM & Media
        Route::apiResource('banners', App\Http\Controllers\Api\Admin\BannerController::class);
        Route::apiResource('videos', App\Http\Controllers\Api\Admin\VideoController::class);
        Route::apiResource('media', App\Http\Controllers\Api\Admin\MediaController::class);
        Route::apiResource('homepage', App\Http\Controllers\Api\Admin\HomepageController::class);
        Route::post('homepage/reorder', [App\Http\Controllers\Api\Admin\HomepageController::class, 'reorder']);
        // Route::apiResource('professionals',  ProfessionalController::class);
        // Route::apiResource('categories',     CategoryController::class);
        // Route::apiResource('services',       ServiceController::class);
        // Route::apiResource('bookings',       BookingController::class);
        // Route::apiResource('orders',         OrderController::class);
    });
});
