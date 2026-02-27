<?php

use Illuminate\Support\Facades\Route;

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
    Route::resource('services', ServiceController::class);
    Route::patch('services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
    Route::resource('packages', PackageController::class);
    Route::patch('packages/{package}/toggle-status', [PackageController::class, 'toggleStatus'])->name('packages.toggle-status');

    // Professionals Sub-routes
    Route::get('professionals/verification', [ProfessionalController::class, 'verification'])->name('professionals.verification');
    Route::get('professionals/verification/{id}/review', [ProfessionalController::class, 'verificationReview'])->name('professionals.verification.review');
    Route::post('professionals/verification/{id}/approve', [ProfessionalController::class, 'approveVerification'])->name('professionals.verification.approve');
    Route::post('professionals/verification/{id}/reject', [ProfessionalController::class, 'rejectVerification'])->name('professionals.verification.reject');
    Route::post('professionals/verification/{id}/request-changes', [ProfessionalController::class, 'requestVerificationChanges'])->name('professionals.verification.request-changes');
    Route::get('professionals/orders', [ProfessionalController::class, 'orders'])->name('professionals.orders');
    Route::get('professionals/history', [ProfessionalController::class, 'history'])->name('professionals.history');

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
    Route::post('/assign/update', [AssignController::class, 'update'])->name('assign.update');
    
    // Settings
    Route::post('settings/update', [SettingController::class, 'update'])->name('settings.update');
    Route::resource('settings', SettingController::class)->except(['update']);

    Route::post('homepage/reorder', [HomepageController::class, 'reorder'])->name('homepage.reorder');
    Route::patch('homepage/{homepage}/toggle-status', [HomepageController::class, 'toggleStatus'])->name('homepage.toggle-status');
    Route::resource('homepage', HomepageController::class);
});
