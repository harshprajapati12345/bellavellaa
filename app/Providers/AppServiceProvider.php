<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── OTP Rate Limiter (5 requests/min per mobile) ──────────
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

        // ── Polymorphic Relation Morph Map ────────────────────────
        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'service' => \App\Models\Service::class,
            'package' => \App\Models\Package::class,
        ]);
    }
}
