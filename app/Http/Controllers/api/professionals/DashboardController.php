<?php

namespace App\Http\Controllers\Api\Professionals;

use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends BaseController
{
    /**
     * GET /api/professionals/dashboard
     * Dashboard summary: today's bookings, earnings overview, pending requests
     */
    public function index(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');
        
        $today = Carbon::today()->toDateString();

        // Today's bookings
        $todaysBookings = Booking::where('professional_id', $professional->id)
            ->where('date', $today)
            ->whereNotIn('status', ['Cancelled', 'Completed'])
            ->orderBy('slot', 'asc')
            ->get();

        // Pending Requests
        $pendingRequestsWait = Booking::whereIn('status', ['Unassigned', 'Pending'])
            ->whereNull('professional_id')
            ->where('city', $professional->city)
            ->count();

        return $this->success([
            'todays_bookings'  => $todaysBookings,
            'pending_requests' => $pendingRequestsWait,
            'todays_earnings'  => Booking::where('professional_id', $professional->id)
                                    ->where('date', $today)
                                    ->where('status', 'Completed')
                                    ->sum('price'), // Approximate, proper earnings logic inside EarningsController
            'total_earnings'   => $professional->earnings,
            'total_orders'     => $professional->orders,
            'rating'           => $professional->rating,
            'is_online'        => (bool) $professional->is_online,
        ], 'Dashboard summary retrieved.');
    }

    /**
     * GET /api/professionals/schedule
     * Upcoming bookings formatted for a calendar/schedule view
     */
    public function schedule(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        // Upcoming bookings (today and future)
        $schedule = Booking::where('professional_id', $professional->id)
            ->where('date', '>=', Carbon::today()->toDateString())
            ->whereNotIn('status', ['Cancelled'])
            ->orderBy('date', 'asc')
            ->orderBy('slot', 'asc')
            ->get();

        return $this->success($schedule, 'Schedule retrieved.');
    }

    /**
     * GET /api/professionals/availability
     */
    public function availability(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        return $this->success([
            'is_online' => (bool) $professional->is_online
        ], 'Availability status retrieved.');
    }

    /**
     * POST /api/professionals/availability
     */
    public function toggleAvailability(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');
        
        $validated = $request->validate([
            'is_online' => 'required|boolean'
        ]);

        $professional->is_online = $validated['is_online'];
        $professional->save();

        return $this->success([
            'is_online' => (bool) $professional->is_online
        ], 'Availability status updated.');
    }
}
