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
        
        // Calculate remaining seconds & progress
        $remainingSeconds = 0;
        $shiftProgress = 0;
        $isOnlineAutoritative = false;

        if ($professional->shift_end_time && now()->lt($professional->shift_end_time)) {
            $isOnlineAutoritative = (bool) $professional->is_online; // Still depends on manual toggle
            $remainingSeconds = max(0, now()->diffInSeconds($professional->shift_end_time, false));
            
            if ($professional->shift_start_time) {
                $totalDuration = $professional->shift_start_time->diffInSeconds($professional->shift_end_time);
                $elapsed = $professional->shift_start_time->diffInSeconds(now());
                $shiftProgress = $totalDuration > 0 ? ($elapsed / $totalDuration) : 0;
            }
        } else {
            // Hard lock offline if shift expired
            if ($professional->is_online) {
                $professional->update(['is_online' => false, 'session_id' => null]);
            }
        }

        // ... (skipping some logic for brevitiy in replace_file_content target match)
        $recentBookings = Booking::with('customer')
            ->where('professional_id', $professional->id)
            ->whereIn('status', ['accepted', 'on_the_way', 'arrived', 'scan_kit', 'in_progress', 'payment_pending'])
            ->orderBy('date', 'asc')
            ->orderBy('slot', 'asc')
            ->get()
            ->map(function (\App\Models\Booking $b) {
                $arr = $b->toArray();
                $arr['customer_name'] = $b->customer?->name ?? 'Customer';
                $arr['client_name'] = $b->customer?->name ?? 'Customer';
                $arr['customer_phone'] = $b->customer?->phone ?? $b->customer?->mobile ?? null;
                return $arr;
            });

        $cashWallet = Wallet::where('holder_type', 'professional')
            ->where('holder_id', $professional->id)
            ->where('type', 'cash')
            ->first();
        $balancePaise = $cashWallet ? $cashWallet->balance : 0;

        // Pending Requests (Only show if kits >= 5)
        $kitCount = \App\Models\KitOrder::where('professional_id', $professional->id)->sum('quantity');
        $pendingRequestsWait = 0;
        if ($kitCount >= 5) {
            $pendingRequestsWait = Booking::whereIn('status', ['unassigned', 'pending'])
                ->whereNull('professional_id')
                ->where('city', $professional->city)
                ->count();
        }

        return $this->success([
            'recent_bookings'   => $recentBookings,
            'pending_requests'  => $pendingRequestsWait,
            'todays_earnings'   => (float) Booking::where('professional_id', $professional->id)
                                    ->where('date', $today)
                                    ->where('status', 'completed')
                                    ->get()
                                    ->sum(fn($b) => $b->price - ($b->commission ?? 0)),
            'total_earnings'    => $professional->earnings,
            'total_orders'      => $professional->orders,
            'kit_count'         => \App\Models\KitOrder::where('professional_id', $professional->id)->sum('quantity'),
            'rating'            => $professional->rating,
            'is_online'         => $isOnlineAutoritative,
            'wallet_balance'    => (float) ($balancePaise / 100),
            'remaining_seconds' => $remainingSeconds,
            'shift_progress'    => round($shiftProgress, 4),
            'shift_duration'    => $professional->shift_duration ?: 480,
            'session_id'        => $professional->session_id,
            'shift_end_time'    => $professional->shift_end_time ? $professional->shift_end_time->toIso8601String() : null,
        ], 'Dashboard summary retrieved.');
    }

    // ... (rest of the methods)

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
            // Prevent double shift activation if already online and shift not expired
            if ($professional->shift_end_time && now()->lt($professional->shift_end_time)) {
                // Shift already active, just ensure manual flag is on
                $professional->is_online = true;
                $professional->save();
                return $this->success(['is_online' => true, 'session_id' => $professional->session_id], 'Shift already active.');
            }

            $kitCount = \App\Models\KitOrder::where('professional_id', $professional->id)->sum('quantity');
            if ($kitCount < 5) {
                return $this->error('Minimum 5 kits required to go online.', 422);
            }

            // Set new shift session
            $durationMinutes = $professional->shift_duration ?: 480; 
            $professional->session_id = (string) \Illuminate\Support\Str::uuid();
            $professional->shift_start_time = now();
            $professional->shift_end_time = now()->addMinutes($durationMinutes);
        } else {
            // Going offline manually: stop shift and clear session
            $professional->shift_end_time = null;
            $professional->session_id = null;
        }

        $professional->is_online = $validated['is_online'];
        $professional->save();

        return $this->success([
            'is_online'      => (bool) $professional->is_online,
            'shift_end_time' => $professional->shift_end_time ? $professional->shift_end_time->toIso8601String() : null,
        ], 'Availability status updated.');
    }

    /**
     * POST /api/professional/update-online-status
     */
    public function updateOnlineStatus(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');
        
        // Auto-offline check in heartbeat
        if ($professional->shift_end_time && now()->greaterThan($professional->shift_end_time)) {
            $professional->is_online = false;
            $professional->session_id = null;
            $professional->save();
        }

        $professional->last_seen = now();
        $professional->save();

        $isOnlineAuthoritative = $professional->is_online && $professional->shift_end_time && now()->lt($professional->shift_end_time);

        return $this->success([
            'status'            => 'updated',
            'is_online'         => $isOnlineAuthoritative,
            'last_seen'         => $professional->last_seen,
            'remaining_seconds' => $isOnlineAuthoritative 
                                    ? max(0, now()->diffInSeconds($professional->shift_end_time, false)) 
                                    : 0
        ], 'Heartbeat received.');
    }
}
