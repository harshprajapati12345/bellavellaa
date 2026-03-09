<?php

namespace App\Http\Controllers\Api\Professionals;

use App\Models\Booking;
use App\Models\Wallet;
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

        // All active bookings assigned to professional
        $recentBookings = Booking::where('professional_id', $professional->id)
            ->whereIn('status', ['Assigned', 'Accepted', 'Started', 'In Progress', 'Arrived'])
            ->orderBy('date', 'asc')
            ->orderBy('slot', 'asc')
            ->get();

        $cashWallet = Wallet::where('holder_type', 'professional')
            ->where('holder_id', $professional->id)
            ->where('type', 'cash')
            ->first();
        $balancePaise = $cashWallet ? $cashWallet->balance : 0;

        // Pending Requests (Only show if deposit >= 1500)
        $pendingRequestsWait = 0;
        if ($balancePaise >= 150000) {
            $pendingRequestsWait = Booking::whereIn('status', ['Unassigned', 'Pending'])
                ->whereNull('professional_id')
                ->where('city', $professional->city)
                ->count();
        }

        return $this->success([
            'recent_bookings'  => $recentBookings,
            'pending_requests' => $pendingRequestsWait,
            'todays_earnings'  => Booking::where('professional_id', $professional->id)
                                    ->where('date', $today)
                                    ->where('status', 'Completed')
                                    ->sum('price'),
            'total_earnings'   => $professional->earnings,
            'total_orders'     => $professional->orders,
            'kit_count'        => \App\Models\KitOrder::where('professional_id', $professional->id)->sum('quantity'),
            'rating'           => $professional->rating,
            'is_online'        => (bool) $professional->is_online,
            'wallet_balance'   => (float) ($balancePaise / 100),
        ], 'Dashboard summary retrieved.');
    }

    /**
     * GET /api/professional/active-job
     */
    public function activeJob(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        // Logic to find current started/in-progress job
        $activeJob = Booking::where('professional_id', $professional->id)
            ->whereIn('status', ['Started', 'In Progress'])
            ->first();

        return $this->success($activeJob, 'Active job retrieved.');
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

        if ($validated['is_online']) {
            $wallet = Wallet::where('holder_type', 'professional')
                ->where('holder_id', $professional->id)
                ->where('type', 'cash')
                ->first();

            $balance = $wallet ? $wallet->balance : 0;
            
            if ($balance < 150000) { // 1500 * 100 paise
                return $this->error('Minimum ₹1,500 deposit required to go online.', 422);
            }
        }

        $professional->is_online = $validated['is_online'];
        $professional->save();

        return $this->success([
            'is_online' => (bool) $professional->is_online
        ], 'Availability status updated.');
    }

    /**
     * POST /api/professional/update-online-status
     */
    public function updateOnlineStatus(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');
        
        $professional->last_seen = now();
        $professional->save();

        return $this->success([
            'status' => 'updated',
            'last_seen' => $professional->last_seen
        ], 'Heartbeat received.');
    }
}
