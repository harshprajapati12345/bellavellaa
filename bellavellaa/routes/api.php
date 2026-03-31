<?php

use App\Http\Controllers\Api\Client\AddressController;
use App\Http\Controllers\Api\Client\BookingController;
use App\Http\Controllers\Api\Client\CategoryController;
use App\Http\Controllers\Api\Client\HomepageController;
use App\Http\Controllers\Api\Client\OfferController as ClientOfferController;
use App\Http\Controllers\Api\Client\ProfileController;
use App\Http\Controllers\Api\Client\ReviewController;
use App\Http\Controllers\Api\Client\CartController;
use App\Http\Controllers\Api\Client\WalletController;
use App\Http\Controllers\Api\Client\AuthController as ClientAuthController;
use App\Http\Controllers\Api\Client\PromotionController;
use App\Http\Controllers\Api\Client\SlotController;
use App\Http\Controllers\Api\Client\NotificationController;
use App\Http\Controllers\Api\Client\PackageController as ClientPackageController;
use App\Http\Controllers\Api\Client\ServiceController as ClientServiceController;
use App\Http\Controllers\Api\Client\ServiceGroupController as ClientServiceGroupController;
use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;

use Illuminate\Support\Facades\Route;

/* |-------------------------------------------------------------------------- | API Routes |-------------------------------------------------------------------------- | | All routes are prefixed with /api automatically by Laravel. | | /api/client       → Customer mobile app     (JWT via OTP) | /api/professional → Professional mobile app   (JWT via OTP) | /api/admin        → Admin panel API         (JWT via email+password) | */

// ═══════════════════════════════════════════════════════════════════
// IMAGE CORS PROXY
// ═══════════════════════════════════════════════════════════════════

Route::get('/images/{path}', function ($path) {
    $path = str_replace('..', '', $path);
    if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
        abort(404);
    }
    return response()->file(\Illuminate\Support\Facades\Storage::disk('public')->path($path));
})->where('path', '.*');

// ═══════════════════════════════════════════════════════════════════
// THEME — Public endpoint (no auth required)
// ═══════════════════════════════════════════════════════════════════
use App\Http\Controllers\Api\ThemeController;
Route::get('theme', [ThemeController::class , 'index']);

// ═══════════════════════════════════════════════════════════════════
// CLIENT — Customer Mobile App
// ═══════════════════════════════════════════════════════════════════


Route::prefix('client')->group(function () {
    // Public Routes
    Route::get('homepage', [HomepageController::class , 'index']);

    // Categories
    Route::get('categories', [\App\Http\Controllers\api\client\ClientCategoryController::class , 'index']);
    Route::get('categories/{slug}', [\App\Http\Controllers\api\client\ClientCategoryController::class , 'show']);
    Route::get('categories/{slug}/screen', [\App\Http\Controllers\api\client\ClientCategoryController::class , 'screenData']);
    Route::get('categories/{slug}/page', [\App\Http\Controllers\api\client\ClientCategoryController::class , 'pageData']);
    Route::get('categories/{slug}/details', [\App\Http\Controllers\api\client\ClientCategoryController::class , 'details']);
    Route::get('categories/{slug}/service-groups', [\App\Http\Controllers\api\client\ClientCategoryController::class , 'serviceGroups']);

    // Dedicated Resource Details
    Route::get('service-groups/{id}', [\App\Http\Controllers\api\client\ClientServiceGroupController::class, 'show']);
    Route::get('services/{id}', [\App\Http\Controllers\api\client\ClientServiceController::class, 'show']);
    Route::get('service-hierarchy/{nodeKey}', [\App\Http\Controllers\Api\Client\ClientServiceHierarchyController::class, 'show']);
    Route::get('packages/featured', [ClientPackageController::class, 'featured']);
    Route::get('packages', [ClientPackageController::class, 'index']);
    Route::get('packages/{package}/config', [ClientPackageController::class, 'config']);
    Route::get('services/{id}/reviews', [\App\Http\Controllers\api\client\ClientReviewController::class, 'index']);

    // Original (Legacy Compatibility)
    Route::get('categories/{categorySlug}/groups', [CategoryController::class , 'groups']);
    Route::get('categories/{categorySlug}/services', [CategoryController::class , 'services']);
    Route::get('categories/{categorySlug}/packages', [CategoryController::class , 'packages']);
    Route::get('categories/{categorySlug}/groups/{groupSlug}/services', [ClientServiceGroupController::class , 'services']);
    Route::get('services/{serviceId}/variants', [ClientServiceController::class , 'variants']);

    // Authentication (No JWT required)
    Route::prefix('auth')->group(function () {
            Route::middleware('throttle:otp')->group(function () {
                    Route::post('send-otp', [ClientAuthController::class , 'sendOtp']);
                    Route::post('verify-otp', [ClientAuthController::class , 'verifyOtp']);
                }
                );
            }
            );

            // Protected Routes (JWT required)
            Route::middleware('auth:api')->group(function () {
            // Auth management
            Route::prefix('auth')->group(function () {
                    Route::get('me', [ClientAuthController::class , 'me']);
                    Route::post('refresh', [ClientAuthController::class , 'refresh']);
                    Route::post('logout', [ClientAuthController::class , 'logout']);
                }
                );

                // Profile & Account
                Route::get('profile', [ProfileController::class , 'show']);
                Route::post('profile/update', [ProfileController::class , 'update']);

                // Wallet
                Route::get('wallet', [WalletController::class , 'index']);
                Route::post('wallet/deposit', [WalletController::class , 'deposit']);
                Route::post('wallet/withdraw', [WalletController::class , 'withdraw']);

                // Addresses
                Route::apiResource('addresses', AddressController::class);

                // Bookings
                Route::get('bookings', [BookingController::class , 'index']);
                Route::get('bookings/{booking}', [BookingController::class , 'show']);
                Route::post('bookings/{booking}/cancel', [BookingController::class , 'cancel']);
                Route::post('bookings/{booking}/reschedule', [BookingController::class , 'reschedule']);

                // Notifications
                Route::get('notifications', [NotificationController::class , 'index']);
                Route::post('notifications/{id}/read', [NotificationController::class , 'markAsRead']);
                Route::post('notifications/read-all', [NotificationController::class , 'markAllAsRead']);
                Route::delete('notifications/{id}', [NotificationController::class , 'destroy']);

                // Cart
                Route::get('cart', [CartController::class , 'index']);
                Route::post('cart', [CartController::class , 'store']);
                Route::put('cart/{cart}', [CartController::class , 'update']);
                Route::delete('cart/{cart}', [CartController::class , 'destroy']);
                Route::post('cart/clear', [CartController::class , 'clear']);
                Route::post('cart/sync', [CartController::class , 'sync']);
                Route::post('cart/checkout/preview', [CartController::class , 'previewCheckout']);
                Route::post('cart/checkout', [CartController::class , 'checkout']);
                Route::post('cart/checkout/verify', [CartController::class , 'verifyCheckout']);

                // Slots
                Route::get('slots-from-cart', [CartController::class , 'getSlotsFromCart']);

                // Reviews
                Route::post('bookings/{bookingId}/reviews', [\App\Http\Controllers\api\client\ClientReviewController::class , 'store']);
                Route::post('review/professional', [\App\Http\Controllers\Api\Client\UserReviewController::class , 'storeProfessionalReview']);
                Route::post('app-feedback', [\App\Http\Controllers\Api\Client\AppFeedbackController::class , 'store']);

                Route::get('offers', [ClientOfferController::class , 'index']);
                Route::post('offers/validate', [ClientOfferController::class , 'validateCode']);

                // Backward-compatibility shim:
                // Keep legacy promotion routes alive until all deployed clients move to /client/offers.
                Route::get('promotions', [PromotionController::class , 'index']);
                Route::post('promotions/validate', [PromotionController::class , 'validateCode']);
                Route::get('slots', [SlotController::class , 'index']);

                // Refer & Earn
                Route::get('referrals', [\App\Http\Controllers\Api\Client\ReferralController::class , 'index']);
            }
            );
        });

// ═══════════════════════════════════════════════════════════════════
// PROFESSIONALS — Professional Mobile App
// ═══════════════════════════════════════════════════════════════════

// ═══════════════════════════════════════════════════════════════════
// PROFESSIONAL — Professional Mobile App
// ═══════════════════════════════════════════════════════════════════

Route::prefix('professional')->group(function () {
    // Auth (No JWT required)
    Route::post('send-otp', [\App\Http\Controllers\Api\Professionals\AuthController::class , 'sendOtp']);
    Route::post('verify-otp', [\App\Http\Controllers\Api\Professionals\AuthController::class , 'verifyOtp']);
    Route::post('register', [\App\Http\Controllers\Api\Professionals\AuthController::class , 'register']);
    Route::post('login', [\App\Http\Controllers\Api\Professionals\AuthController::class , 'login']);

    // Protected Routes (JWT required)
    Route::middleware(['auth:professional-api'])->group(function () {
        // Essential routes allowed even if suspended
        Route::get('me', [\App\Http\Controllers\Api\Professionals\AuthController::class , 'me']);
        Route::get('verification-status', [\App\Http\Controllers\Api\Professionals\AuthController::class , 'verificationStatus']);
        Route::post('refresh', [\App\Http\Controllers\Api\Professionals\AuthController::class , 'refresh']);
        Route::post('logout', [\App\Http\Controllers\Api\Professionals\AuthController::class , 'logout']);
        Route::get('profile', [\App\Http\Controllers\Api\Professionals\ProfileController::class , 'show']);
        Route::post('update-fcm-token', [\App\Http\Controllers\Api\Professionals\ProfileController::class , 'updateFcmToken']);
        Route::post('update-bank-details', [\App\Http\Controllers\Api\Professionals\ProfileController::class , 'updateBankDetails']);
        Route::post('update-upi-details', [\App\Http\Controllers\Api\Professionals\ProfileController::class , 'updateUPIDetails']);

        // Routes blocked if suspended
        Route::middleware(['professional.suspended'])->group(function () {
            // Dashboard
            Route::get('dashboard', [\App\Http\Controllers\Api\Professionals\DashboardController::class , 'index']);
            Route::get('active-job', [\App\Http\Controllers\Api\Professionals\DashboardController::class , 'activeJob']);
            Route::post('toggle-availability', [\App\Http\Controllers\Api\Professionals\DashboardController::class , 'toggleAvailability']);
            Route::post('update-online-status', [\App\Http\Controllers\Api\Professionals\DashboardController::class , 'updateOnlineStatus']);
            Route::get('leaderboard', [\App\Http\Controllers\Api\Professionals\DashboardController::class , 'leaderboard']);

            // Booking Requests
            Route::get('booking-requests', [\App\Http\Controllers\Api\Professionals\BookingController::class , 'requests']);
            Route::get('bookings', [\App\Http\Controllers\Api\Professionals\BookingController::class , 'index']);
            Route::get('bookings/{id}', [\App\Http\Controllers\Api\Professionals\BookingController::class , 'show']);
            Route::post('bookings/{id}/accept', [\App\Http\Controllers\Api\Professionals\BookingController::class , 'accept']);
            Route::post('bookings/{id}/reject', [\App\Http\Controllers\Api\Professionals\BookingController::class , 'reject']);

            // Job Workflow
            Route::post('jobs/{id}/arrived', [\App\Http\Controllers\Api\Professionals\JobController::class , 'arrived']);
            Route::post('jobs/{id}/start-journey', [\App\Http\Controllers\Api\Professionals\JobController::class , 'startJourney']);
            Route::post('jobs/{id}/start-service', [\App\Http\Controllers\Api\Professionals\JobController::class , 'startService']);
            Route::post('jobs/{id}/finish-service', [\App\Http\Controllers\Api\Professionals\JobController::class , 'finishService']);
            Route::post('jobs/{id}/scan-kit', [\App\Http\Controllers\Api\Professionals\JobController::class , 'scanKit']);
            Route::post('jobs/{id}/complete', [\App\Http\Controllers\Api\Professionals\JobController::class , 'complete']);
            Route::post('jobs/{id}/payment-confirm', [\App\Http\Controllers\Api\Professionals\JobController::class , 'paymentConfirm']);
            Route::prefix('jobs/{id}/payment')->group(function () {
                    Route::post('create-order', [\App\Http\Controllers\Api\Professionals\JobController::class , 'createPaymentOrder']);
                    Route::post('verify', [\App\Http\Controllers\Api\Professionals\JobController::class , 'verifyPayment']);
                }
                );

                // Earnings & Wallet
                Route::get('earnings', [\App\Http\Controllers\Api\Professionals\EarningsController::class , 'index']);
                Route::get('wallet', [\App\Http\Controllers\Api\Professionals\EarningsController::class , 'wallet']);
                Route::prefix('wallet/deposit')->group(function () {
                    Route::post('create-order', [\App\Http\Controllers\Api\Professionals\EarningsController::class , 'createDepositOrder']);
                    Route::post('verify', [\App\Http\Controllers\Api\Professionals\EarningsController::class , 'verifyDeposit']);
                }
                );
                Route::get('jobs-history', [\App\Http\Controllers\Api\Professionals\EarningsController::class , 'history']);
                Route::get('schedule', [\App\Http\Controllers\Api\Professionals\DashboardController::class , 'schedule']);
                Route::post('schedule/slots', [\App\Http\Controllers\Api\Professionals\DashboardController::class , 'updateSlots']);
                Route::get('withdrawals/history', [\App\Http\Controllers\Api\Professionals\WithdrawalController::class , 'history']);
                Route::post('withdraw', [\App\Http\Controllers\Api\Professionals\WithdrawalController::class , 'store']);

                // Kit Store / Orders
                Route::get('kit-products', [\App\Http\Controllers\Api\Professionals\KitController::class , 'products']);
                Route::post('orders', [\App\Http\Controllers\Api\Professionals\KitController::class , 'order']);
                Route::get('orders', [\App\Http\Controllers\Api\Professionals\KitController::class , 'orders']);
                Route::get('orders/{id}', [\App\Http\Controllers\Api\Professionals\KitController::class , 'showOrder']);

                // Kit Payment
                Route::post('payment/create-order', [\App\Http\Controllers\Api\Professionals\KitController::class , 'createPaymentOrder']);
                Route::post('payment/verify', [\App\Http\Controllers\Api\Professionals\KitController::class , 'verifyPayment']);

                // Notifications
                Route::get('notifications', [\App\Http\Controllers\Api\Professionals\NotificationController::class , 'index']);
                Route::post('notifications/{id}/read', [\App\Http\Controllers\Api\Professionals\NotificationController::class , 'read']);
                Route::post('notifications/read-all', [\App\Http\Controllers\Api\Professionals\NotificationController::class , 'readAll']);
                Route::delete('notifications/{id}', [\App\Http\Controllers\Api\Professionals\NotificationController::class , 'destroy']);

                // User Reviews
                Route::post('review/client', [\App\Http\Controllers\Api\Professionals\UserReviewController::class , 'storeClientReview']);

                // Referrals
                Route::get('referrals', [\App\Http\Controllers\Api\Professionals\ReferralController::class , 'index']);
                Route::post('referrals/submit', [\App\Http\Controllers\Api\Professionals\ReferralController::class , 'submit']);

                // Leave Requests
                Route::get('leaves', [\App\Http\Controllers\Api\Professionals\LeaveController::class , 'index']);
                Route::post('leaves', [\App\Http\Controllers\Api\Professionals\LeaveController::class , 'store']);
                Route::delete('leaves/{id}', [\App\Http\Controllers\Api\Professionals\LeaveController::class , 'destroy']);

                // Profile
                Route::put('profile', [\App\Http\Controllers\Api\Professionals\ProfileController::class , 'update']);
                Route::post('upload-profile-image', [\App\Http\Controllers\Api\Professionals\ProfileController::class , 'uploadProfileImage']);
                Route::post('upload-documents', [\App\Http\Controllers\Api\Professionals\ProfileController::class , 'uploadDocuments']);
                Route::put('change-password', [\App\Http\Controllers\Api\Professionals\ProfileController::class , 'changePassword']);
            }
            );
        });
    });



// ═══════════════════════════════════════════════════════════════════
// ADMIN — Admin Panel API
// ═══════════════════════════════════════════════════════════════════


Route::prefix('admin')->group(function () {

    Route::prefix('auth')->group(function () {

            // Public — no JWT required
            Route::post('login', [AdminAuthController::class , 'login']);

            // Protected — valid admin JWT required
            Route::middleware('jwt.admin')->group(function () {
                    Route::get('me', [AdminAuthController::class , 'me']);
                    Route::post('refresh', [AdminAuthController::class , 'refresh']);
                    Route::post('logout', [AdminAuthController::class , 'logout']);
                }
                );
            }
            );

            // Future admin routes ─────────────────────────────────────────
            Route::middleware('jwt.admin')->group(function () {
            Route::apiResource('customers', \App\Http\Controllers\Api\Admin\CustomerController::class);
            Route::apiResource('packages', \App\Http\Controllers\Api\Admin\PackageController::class);
            Route::apiResource('services', \App\Http\Controllers\Api\Admin\ServiceController::class);
            Route::apiResource('offers', \App\Http\Controllers\Api\Admin\OfferController::class);
            Route::apiResource('reviews', \App\Http\Controllers\Api\Admin\ReviewController::class)->except(['store']);

            // Settings - Bulk update pattern
            Route::get('settings', [App\Http\Controllers\Api\Admin\SettingController::class , 'index']);
            Route::get('settings/{key}', [App\Http\Controllers\Api\Admin\SettingController::class , 'show']);
            Route::post('settings', [App\Http\Controllers\Api\Admin\SettingController::class , 'update']);

            // Assignments
            Route::get('assignments', [App\Http\Controllers\Api\Admin\AssignmentController::class , 'index']);
            Route::post('assignments', [App\Http\Controllers\Api\Admin\AssignmentController::class , 'store']);

            // CRM & Media
            Route::apiResource('banners', App\Http\Controllers\Api\Admin\BannerController::class);
            Route::apiResource('videos', App\Http\Controllers\Api\Admin\VideoController::class);
            Route::apiResource('media', App\Http\Controllers\Api\Admin\MediaController::class);
            Route::apiResource('homepage', App\Http\Controllers\Api\Admin\HomepageController::class);
            Route::post('homepage/reorder', [App\Http\Controllers\Api\Admin\HomepageController::class , 'reorder']);

            // Areas
            Route::get('areas', function () {
                return response()->json([
                    'success' => true,
                    'data' => \App\Models\Customer::whereNotNull('area')->distinct()->pluck('area')
                ]);
            });

            // Professionals Management
            Route::apiResource('professionals', App\Http\Controllers\Api\Admin\ProfessionalController::class);
            Route::get('professionals-verification', [
                App\Http\Controllers\Api\Admin\ProfessionalVerificationController::class ,
                'index'
            ]);
            Route::post('professionals/{id}/verify', [
                App\Http\Controllers\Api\Admin\ProfessionalVerificationController::class ,
                'verify'
            ]);
            Route::get('professionals/{id}/orders', [App\Http\Controllers\Api\Admin\ProfessionalOrderController::class , 'index']);
            Route::get('professionals/{id}/history', [
                App\Http\Controllers\Api\Admin\ProfessionalOrderController::class ,
                'history'
            ]);
            Route::apiResource('leave-requests', App\Http\Controllers\Api\Admin\LeaveRequestController::class);

            // Kit Management
            Route::prefix('kit')->group(function () {
                    Route::apiResource('products', App\Http\Controllers\Api\Admin\KitProductController::class);
                    Route::apiResource('orders', App\Http\Controllers\Api\Admin\KitOrderController::class);
                }
                );

                // Withdrawal Management
                Route::prefix('withdrawals')->group(function () {
                    Route::get('/', [\App\Http\Controllers\Api\Admin\WithdrawalController::class , 'index']);
                    Route::post('{id}/approve', [\App\Http\Controllers\Api\Admin\WithdrawalController::class , 'approve']);
                    Route::post('{id}/reject', [\App\Http\Controllers\Api\Admin\WithdrawalController::class , 'reject']);
                }
                );
            }
            );
        });
