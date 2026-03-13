<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        RateLimiter::for('otp', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('mobile', $request->ip()));
        });

        \Illuminate\Support\Facades\View::composer('layouts.sidebar', function ($view) {
            $pendingVerificationCount = \App\Models\Professional::where('verification', 'Pending')->count();
            $pendingLeaveCount = \App\Models\LeaveRequest::where('status', 'Pending')->count();
            $totalProNotificationCount = $pendingVerificationCount + $pendingLeaveCount;

            $view->with([
                'pendingVerificationCount' => $pendingVerificationCount,
                'pendingLeaveCount' => $pendingLeaveCount,
                'totalProNotificationCount' => $totalProNotificationCount,
            ]);
        });

        Relation::morphMap([
            'category' => \App\Models\Category::class,
            'service_group' => \App\Models\ServiceGroup::class,
            'service_type' => \App\Models\ServiceType::class,
            'service' => \App\Models\Service::class,
            'variant' => \App\Models\ServiceVariant::class,
            'package' => \App\Models\Package::class,
            'professional' => \App\Models\Professional::class,
            'customer' => \App\Models\Customer::class,
            'client' => \App\Models\Customer::class,
        ]);
    }
}
