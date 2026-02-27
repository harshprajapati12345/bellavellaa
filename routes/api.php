<?php

use App\Http\Controllers\Api\Client\AddressController;
use App\Http\Controllers\Api\Client\BookingController;
use App\Http\Controllers\Api\Client\CategoryController;
use App\Http\Controllers\Api\Client\HomepageController;
use App\Http\Controllers\Api\Client\ProfileController;
use App\Http\Controllers\Api\Client\ReviewController;
use App\Http\Controllers\Api\Client\CartController;
use App\Http\Controllers\Api\Client\WalletController;
use App\Http\Controllers\Api\Client\AuthController as ClientAuthController;
use App\Http\Controllers\Api\Client\PromotionController;
use App\Http\Controllers\Api\Client\SlotController;
use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api automatically by Laravel.
|
| /api/client       → Customer mobile app     (JWT via OTP)
| /api/professional → Professional mobile app   (JWT via OTP)
| /api/admin        → Admin panel API         (JWT via email+password)
|
*/

// ═══════════════════════════════════════════════════════════════════
// CLIENT — Customer Mobile App
// ═══════════════════════════════════════════════════════════════════

Route::prefix('client')->group(function () {
    // Public Routes
    Route::get('homepage', [HomepageController::class, 'index']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{slug}', [CategoryController::class, 'show']);

    // Authentication (No JWT required)
    Route::prefix('auth')->group(function () {
        Route::middleware('throttle:otp')->group(function () {
            Route::post('send-otp', [ClientAuthController::class, 'sendOtp']);
            Route::post('verify-otp', [ClientAuthController::class, 'verifyOtp']);
        });
    });

    // Protected Routes (JWT required)
    Route::middleware('auth:api')->group(function () {
        // Auth management
        Route::prefix('auth')->group(function () {
            Route::get('me', [ClientAuthController::class, 'me']);
            Route::post('refresh', [ClientAuthController::class, 'refresh']);
            Route::post('logout', [ClientAuthController::class, 'logout']);
        });

        // Profile & Account
        Route::get('profile', [ProfileController::class, 'show']);
        Route::post('profile/update', [ProfileController::class, 'update']);

        // Wallet
        Route::get('wallet', [WalletController::class, 'index']);

        // Addresses
        Route::apiResource('addresses', AddressController::class);

        // Bookings
        Route::get('bookings', [BookingController::class, 'index']);
        Route::get('bookings/{booking}', [BookingController::class, 'show']);
        Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel']);

        // Cart
        Route::get('cart', [CartController::class, 'index']);
        Route::post('cart', [CartController::class, 'store']);
        Route::put('cart/{cart}', [CartController::class, 'update']);
        Route::delete('cart/{cart}', [CartController::class, 'destroy']);
        Route::post('cart/clear', [CartController::class, 'clear']);
        Route::post('cart/checkout', [CartController::class, 'checkout']);

        // Reviews
        Route::post('reviews', [ReviewController::class, 'store']);

        // Promotions & Slots
        Route::get('promotions', [PromotionController::class, 'index']);
        Route::post('promotions/validate', [PromotionController::class, 'validateCode']);
        Route::get('slots', [SlotController::class, 'index']);
    });
});

// ═══════════════════════════════════════════════════════════════════
// PROFESSIONALS — Professional Mobile App
// ═══════════════════════════════════════════════════════════════════

Route::prefix('professionals')->group(function () {
    Route::prefix('auth')->group(function () {
        // Public — no JWT required
        Route::middleware('throttle:otp')->group(function () {
            Route::post('send-otp', [\App\Http\Controllers\Api\Professionals\AuthController::class, 'sendOtp']);
            Route::post('verify-otp', [\App\Http\Controllers\Api\Professionals\AuthController::class, 'verifyOtp']);
            Route::post('signup', [\App\Http\Controllers\Api\Professionals\AuthController::class, 'signup']);
        });

        // Protected — valid professional JWT required
        Route::middleware('auth:professional-api')->group(function () {
            Route::get('me', [\App\Http\Controllers\Api\Professionals\AuthController::class, 'me']);
            Route::get('status', [\App\Http\Controllers\Api\Professionals\AuthController::class, 'status']);
            Route::post('refresh', [\App\Http\Controllers\Api\Professionals\AuthController::class, 'refresh']);
            Route::post('logout', [\App\Http\Controllers\Api\Professionals\AuthController::class, 'logout']);
        });
    });

    Route::middleware('auth:professional-api')->group(function () {
        // Profile
        Route::get('profile', [\App\Http\Controllers\Api\Professionals\ProfileController::class, 'show']);
        Route::post('profile/update', [\App\Http\Controllers\Api\Professionals\ProfileController::class, 'update']);

        // Bookings & Orders
        Route::get('bookings/requests', [\App\Http\Controllers\Api\Professionals\BookingController::class, 'requests']);
        Route::get('bookings', [\App\Http\Controllers\Api\Professionals\BookingController::class, 'index']);
        Route::get('bookings/{booking}', [\App\Http\Controllers\Api\Professionals\BookingController::class, 'show']);
        Route::post('bookings/{booking}/accept', [\App\Http\Controllers\Api\Professionals\BookingController::class, 'accept']);
        Route::post('bookings/{booking}/reject', [\App\Http\Controllers\Api\Professionals\BookingController::class, 'reject']);
        Route::post('bookings/{booking}/status', [\App\Http\Controllers\Api\Professionals\BookingController::class, 'updateStatus']);

        // Dashboard & Schedule
        Route::get('dashboard', [\App\Http\Controllers\Api\Professionals\DashboardController::class, 'index']);
        Route::get('schedule', [\App\Http\Controllers\Api\Professionals\DashboardController::class, 'schedule']);
        Route::get('availability', [\App\Http\Controllers\Api\Professionals\DashboardController::class, 'availability']);
        Route::post('availability', [\App\Http\Controllers\Api\Professionals\DashboardController::class, 'toggleAvailability']);

        // Earnings & Wallet
        Route::get('earnings', [\App\Http\Controllers\Api\Professionals\EarningsController::class, 'index']);
        Route::get('jobs/history', [\App\Http\Controllers\Api\Professionals\EarningsController::class, 'history']);
        Route::get('wallet', [\App\Http\Controllers\Api\Professionals\EarningsController::class, 'wallet']);
        Route::post('wallet/withdraw', [\App\Http\Controllers\Api\Professionals\EarningsController::class, 'withdraw']);

        // Kit Management
        Route::get('kit-store', [\App\Http\Controllers\Api\Professionals\KitController::class, 'store']);
        Route::post('kit-orders', [\App\Http\Controllers\Api\Professionals\KitController::class, 'order']);

        // Notifications
        Route::get('notifications', [\App\Http\Controllers\Api\Professionals\NotificationController::class, 'index']);
        Route::post('notifications/read', [\App\Http\Controllers\Api\Professionals\NotificationController::class, 'markAsRead']);
    });
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
        Route::apiResource('packages', \App\Http\Controllers\Api\Admin\PackageController::class);
        Route::apiResource('services', \App\Http\Controllers\Api\Admin\ServiceController::class);
        Route::apiResource('offers', \App\Http\Controllers\Api\Admin\OfferController::class);
        Route::apiResource('reviews', \App\Http\Controllers\Api\Admin\ReviewController::class)->except(['store']);

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

        // Professionals Management
        Route::apiResource('professionals', App\Http\Controllers\Api\Admin\ProfessionalController::class);
        Route::get('professionals-verification', [
            App\Http\Controllers\Api\Admin\ProfessionalVerificationController::class,
            'index'
        ]);
        Route::post('professionals/{id}/verify', [
            App\Http\Controllers\Api\Admin\ProfessionalVerificationController::class,
            'verify'
        ]);
        Route::get('professionals/{id}/orders', [App\Http\Controllers\Api\Admin\ProfessionalOrderController::class, 'index']);
        Route::get('professionals/{id}/history', [
            App\Http\Controllers\Api\Admin\ProfessionalOrderController::class,
            'history'
        ]);
        Route::apiResource('leave-requests', App\Http\Controllers\Api\Admin\LeaveRequestController::class);

        // Kit Management
        Route::prefix('kit')->group(function () {
            Route::apiResource('products', App\Http\Controllers\Api\Admin\KitProductController::class);
            Route::apiResource('orders', App\Http\Controllers\Api\Admin\KitOrderController::class);
        });
    });
});