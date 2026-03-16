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
        $recentBookings = Booking::with('customer')
            ->where('professional_id', $professional->id)
            ->whereIn('status', ['assigned', 'accepted', 'in_progress', 'arrived'])
            ->orderBy('date', 'asc')
            ->orderBy('slot', 'asc')
            ->get()
            ->map(function (\App\Models\Booking $b) {
                $arr = $b->toArray();
                $arr['customer_phone'] = $b->customer?->phone ?? $b->customer?->mobile ?? null;
                return $arr;
            });

        $cashWallet = Wallet::where('holder_type', 'professional')
            ->where('holder_id', $professional->id)
            ->where('type', 'cash')
            ->first();
        $balancePaise = $cashWallet ? $cashWallet->balance : 0;

        // Pending Requests (Only show if deposit >= 1500)
        $pendingRequestsWait = 0;
        if ($balancePaise >= 150000) {
            $pendingRequestsWait = Booking::whereIn('status', ['unassigned', 'pending'])
                ->whereNull('professional_id')
                ->where('city', $professional->city)
                ->count();
        }

        return $this->success([
            'recent_bookings'  => $recentBookings,
            'pending_requests' => $pendingRequestsWait,
            'todays_earnings'  => (float) Booking::where('professional_id', $professional->id)
                                    ->where('date', $today)
                                    ->where('status', 'completed')
                                    ->get()
                                    ->sum(fn($b) => $b->price - ($b->commission ?? 0)),
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

        // Safety check for professional object and offline status
        if (!$professional || !$professional->is_online) {
            return $this->success(null, 'Professional is offline or not found.');
        }

        // We include all active workflow statuses so the card stays visible
        $booking = Booking::with('customer')
            ->where('professional_id', $professional->id)
            ->whereIn('status', ['accepted', 'on_the_way', 'arrived', 'in_progress', 'payment_pending'])
            ->latest()
            ->first();

        if (!$booking) {
            return $this->success(null, 'No active job.');
        }

        // Append customer phone so Flutter can open the dialer
        $data = $booking->toArray();
        $data['customer_phone'] = $booking->customer?->phone ?? $booking->customer?->mobile ?? null;

        return $this->success($data, 'Active job retrieved.');
    }

    /**
     * GET /api/professional/schedule?date=YYYY-MM-DD
     * Bookings for a specific date (defaults to today) + slot availability
     */
    public function schedule(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $date = $request->query('date', Carbon::today()->toDateString());

        $bookings = Booking::where('professional_id', $professional->id)
            ->where('date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('slot', 'asc')
            ->get()
            ->map(fn($b) => [
                'id'           => $b->id,
                'slot'         => $b->slot,
                'service_name' => $b->service_name,
                'customer_name'=> $b->customer_name ?? 'Client',
                'status'       => $b->status,
                'price'        => $b->price,
            ]);

        // Slot availability stored in working_hours JSON on the professional
        $wh = $professional->working_hours ?? [];
        $slots = [
            'morning'   => $wh['morning_slot']   ?? true,
            'afternoon' => $wh['afternoon_slot']  ?? true,
            'evening'   => $wh['evening_slot']    ?? false,
        ];

        return $this->success([
            'date'      => $date,
            'bookings'  => $bookings,
            'slots'     => $slots,
        ], 'Schedule retrieved.');
    }

    /**
     * POST /api/professional/schedule/slots
     * Toggle morning/afternoon/evening slot availability
     */
    public function updateSlots(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $validated = $request->validate([
            'morning'   => 'sometimes|boolean',
            'afternoon' => 'sometimes|boolean',
            'evening'   => 'sometimes|boolean',
        ]);

        $wh = $professional->working_hours ?? [];
        if (isset($validated['morning']))   $wh['morning_slot']   = $validated['morning'];
        if (isset($validated['afternoon'])) $wh['afternoon_slot'] = $validated['afternoon'];
        if (isset($validated['evening']))   $wh['evening_slot']   = $validated['evening'];

        $professional->working_hours = $wh;
        $professional->save();

        return $this->success([
            'morning'   => $wh['morning_slot']   ?? true,
            'afternoon' => $wh['afternoon_slot']  ?? true,
            'evening'   => $wh['evening_slot']    ?? false,
        ], 'Slot availability updated.');
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
