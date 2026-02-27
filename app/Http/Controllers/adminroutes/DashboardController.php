<?php

namespace App\Http\Controllers\adminroutes;

use Illuminate\Http\Request;

use App\Models\Booking;
use App\Models\Professional;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Review;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        $stats = [
            'bookings_today' => Booking::whereDate('date', $today)->count(),
            'revenue_today' => Booking::whereDate('date', $today)->where('status', 'Completed')->sum('price'), // Need price in bookings
            'active_professionals' => Professional::where('status', 'Active')->count(),
            'total_customers' => Customer::count(),
            'new_reviews' => Review::whereDate('created_at', '>=', $today->subDays(7))->count(),
            'total_services' => Service::count(),
        ];

        $appointments = Booking::with(['service', 'professional', 'customer'])
            ->whereDate('date', Carbon::today())
            ->orderBy('slot', 'asc')
            ->get();

        $recentBookings = Booking::with('customer')->latest()->limit(5)->get();
        $recentReviews = Review::with('customer')->latest()->limit(3)->get();

        return view('dashboard.index', [
            'bookingsToday' => $stats['bookings_today'],
            'todayRevenue' => number_format($stats['revenue_today']),
            'activeProfessionals' => $stats['active_professionals'],
            'totalCustomers' => $stats['total_customers'],
            'newReviews' => $stats['new_reviews'],
            'totalServices' => $stats['total_services'],
            'appointments' => $appointments,
            'recentBookings' => $recentBookings,
            'recentReviews' => $recentReviews
        ]);
    }
}
