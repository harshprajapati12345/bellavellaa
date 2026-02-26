<?php

namespace App\Providers;

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
    }
}
