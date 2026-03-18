<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\adminroutes\DashboardController;
use App\Http\Controllers\adminroutes\AuthController;
use App\Http\Controllers\adminroutes\CategoryController;
use App\Http\Controllers\adminroutes\ServiceController;
use App\Http\Controllers\adminroutes\PackageController;
use App\Http\Controllers\adminroutes\ProfessionalController;
use App\Http\Controllers\adminroutes\CustomerController;
use App\Http\Controllers\adminroutes\OfferController;
use App\Http\Controllers\adminroutes\ReviewController;
use App\Http\Controllers\adminroutes\MediaController;
use App\Http\Controllers\adminroutes\AssignController;
use App\Http\Controllers\adminroutes\HomepageController;
use App\Http\Controllers\adminroutes\KitProductController;
use App\Http\Controllers\adminroutes\KitOrderController;
use App\Http\Controllers\adminroutes\LeaveRequestController;
use App\Http\Controllers\adminroutes\SettingController;
use App\Http\Controllers\adminroutes\ReferralController;
use App\Http\Controllers\adminroutes\RewardSettingController;
use App\Http\Controllers\adminroutes\ServiceGroupController;
use App\Http\Controllers\adminroutes\ServiceVariantController;
use App\Http\Controllers\adminroutes\ServiceTypeController;
use App\Http\Controllers\adminroutes\CategoryBannerController;
use App\Http\Controllers\adminroutes\HierarchyBannerController;

// ─── Storage File Server ───────────────────────────────────────────────────────
// Workaround for Windows + artisan serve: the public/storage symlink may not
// work correctly on Windows. This route streams files directly from disk.
Route::get('/storage/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . $path);

    if (!file_exists($fullPath)) {
        abort(404);
    }

    $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';

    return response()->file($fullPath, [
        'Content-Type'                => $mimeType,
        'Access-Control-Allow-Origin' => '*',
        'Cache-Control'               => 'public, max-age=86400',
    ]);
})->where('path', '.*');
// ──────────────────────────────────────────────────────────────────────────────

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class);
    Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

    Route::resource('category-banners', CategoryBannerController::class);
    Route::patch('category-banners/{category_banner}/toggle-status', [CategoryBannerController::class, 'toggleStatus'])->name('category-banners.toggle-status');
    Route::resource('hierarchy-banners', HierarchyBannerController::class)->except(['show']);
    Route::patch('hierarchy-banners/{hierarchy_banner}/toggle-status', [HierarchyBannerController::class, 'toggleStatus'])->name('hierarchy-banners.toggle-status');

    // Service Groups (sub-tier under service-type categories)
    Route::resource('service-groups', ServiceGroupController::class);
    Route::resource('service-types', ServiceTypeController::class);
    // AJAX helper: returns service groups for a given category (used by service create/edit form)
    Route::get('categories/{category}/service-groups', [ServiceGroupController::class, 'byCategory'])->name('categories.service-groups');

    Route::resource('services', ServiceController::class);
    Route::patch('services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
    
    // Service Variants (AJAX CRUD)
    Route::post('services/{service}/variants', [ServiceVariantController::class, 'store'])->name('services.variants.store');
    Route::patch('service-variants/{variant}', [ServiceVariantController::class, 'update'])->name('service-variants.update');
    Route::delete('service-variants/{variant}', [ServiceVariantController::class, 'destroy'])->name('service-variants.destroy');
    Route::resource('packages', PackageController::class);
    Route::get('packages-linked-groups', [PackageController::class, 'linkedGroups'])->name('packages.linked-groups');
    Route::get('packages-linked-group-items', [PackageController::class, 'linkedGroupItems'])->name('packages.linked-group-items');
    Route::patch('packages/{package}/toggle-status', [PackageController::class, 'toggleStatus'])->name('packages.toggle-status');

    // Professionals Sub-routes
    Route::get('professionals/verification', [ProfessionalController::class, 'verification'])->name('professionals.verification');
    Route::get('professionals/verification/{id}/review', [ProfessionalController::class, 'verificationReview'])->name('professionals.verification.review');
    Route::post('professionals/verification/{id}/approve', [ProfessionalController::class, 'approveVerification'])->name('professionals.verification.approve');
    Route::post('professionals/verification/{id}/reject', [ProfessionalController::class, 'rejectVerification'])->name('professionals.verification.reject');
    Route::post('professionals/verification/{id}/request-changes', [ProfessionalController::class, 'requestVerificationChanges'])->name('professionals.verification.request-changes');
    Route::get('professionals/orders', [ProfessionalController::class, 'orders'])->name('professionals.orders');
    Route::get('professionals/history', [ProfessionalController::class, 'history'])->name('professionals.history');
    Route::get('professionals/deposits', [ProfessionalController::class, 'deposits'])->name('professionals.deposits');

    // Kit Management
    Route::get('professionals/kit-products', [KitProductController::class, 'index'])->name('kit-products.index');
    Route::resource('professionals/kit-products', KitProductController::class)->except(['index'])->names([
        'create' => 'kit-products.create',
        'store' => 'kit-products.store',
        'edit' => 'kit-products.edit',
        'update' => 'kit-products.update',
        'destroy' => 'kit-products.destroy',
    ]);
    Route::get('professionals/kit-orders', [KitOrderController::class, 'index'])->name('kit-orders.index');
    Route::post('professionals/kit-orders', [KitOrderController::class, 'store'])->name('kit-orders.store');
    Route::get('professionals/kit-orders/history', [KitOrderController::class, 'history'])->name('kit-orders.history');
    Route::patch('professionals/kit-orders/{kitOrder}/status', [KitOrderController::class, 'updateDeliveryStatus'])->name('kit-orders.update-status');

    // Leave Requests
    Route::get('professionals/leaves', [LeaveRequestController::class, 'index'])->name('leaves.index');
    Route::post('professionals/leaves/{leave}/approve', [LeaveRequestController::class, 'approve'])->name('leaves.approve');
    Route::post('professionals/leaves/{leave}/reject', [LeaveRequestController::class, 'reject'])->name('leaves.reject');

    // Professionals Resource (Should be AFTER sub-routes)
    Route::resource('professionals', ProfessionalController::class);

    Route::resource('customers', CustomerController::class);
    Route::patch('customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
    Route::resource('offers', OfferController::class);
    Route::resource('reviews', ReviewController::class);
    Route::post('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
    Route::post('reviews/{review}/toggle-featured', [ReviewController::class, 'toggleFeatured'])->name('reviews.toggle-featured');
    Route::post('reviews/{review}/award-points', [ReviewController::class, 'awardPoints'])->name('reviews.award-points');

    Route::get('media/banners', [MediaController::class, 'banners'])->name('media.banners.index');
    Route::get('media/videos', [MediaController::class, 'videos'])->name('media.videos.index');
    Route::resource('media', MediaController::class);

    Route::get('/assign', [AssignController::class, 'index'])->name('assign.index');
    Route::get('/assign/{id}', [AssignController::class, 'show'])->name('assign.show');
    Route::post('/assign/update', [AssignController::class, 'update'])->name('assign.update');
    Route::post('/assign/auto', [AssignController::class, 'autoAssign'])->name('assign.auto');



    // Settings
    Route::post('settings/update', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/theme/save', [SettingController::class, 'saveTheme'])->name('settings.theme.save');
    Route::post('settings/theme/reset', [SettingController::class, 'resetTheme'])->name('settings.theme.reset');
    Route::resource('settings', SettingController::class)->except(['update']);

    Route::post('homepage/reorder', [HomepageController::class, 'reorder'])->name('homepage.reorder');
    Route::patch('homepage/{homepage}/toggle-status', [HomepageController::class, 'toggleStatus'])->name('homepage.toggle-status');
    Route::resource('homepage', HomepageController::class);

    // Refer & Earn
    Route::get('referrals', [ReferralController::class, 'index'])->name('referrals.index');
    Route::get('referrals/{id}', [ReferralController::class, 'show'])->name('referrals.show');
    Route::post('referrals/{id}/toggle-status', [ReferralController::class, 'toggleStatus'])->name('referrals.toggle-status');


});
