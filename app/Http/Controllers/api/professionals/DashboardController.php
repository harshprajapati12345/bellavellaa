<?php

namespace App\Http\Controllers\Api\Professionals;

use App\Models\Booking;
use App\Models\Wallet;
use App\Models\Setting;
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
        
        // ─── Global Shift Logic (Admin Controlled) ───────────────────
        $shiftStartTime = Setting::get('shift_start_time', '09:00');
        $shiftDuration = (int) Setting::get('shift_duration', 480);

        $shiftStart = Carbon::today()->setTimeFromTimeString($shiftStartTime);
        $shiftEnd = $shiftStart->copy()->addMinutes($shiftDuration);

        // Handle overnight shift (e.g., 10 PM to 6 AM)
        if ($shiftEnd->lt($shiftStart)) {
            $shiftEnd->addDay();
        }

        $isWithinShift = now()->between($shiftStart, $shiftEnd);
        $remainingSeconds = now()->diffInSeconds($shiftEnd, false);
        $isOnlineAuthoritative = false;

        if ($isWithinShift) {
            $isOnlineAuthoritative = (bool) $professional->is_online;
        } else {
            // Hard lock offline if outside shift hours
            if ($professional->is_online) {
                $professional->update(['is_online' => false, 'session_id' => null]);
            }
        }

        $totalDurationSeconds = $shiftDuration * 60;
        $elapsedSeconds = $shiftStart->diffInSeconds(now());
        $shiftProgress = $totalDurationSeconds > 0 ? ($elapsedSeconds / $totalDurationSeconds) : 0;
        $shiftProgress = max(0, min(1, $shiftProgress)); // Clamp between 0 and 1

        $recentBookings = Booking::with(['customer', 'service', 'package'])
            ->where('professional_id', $professional->id)
            ->whereIn('status', ['accepted', 'on_the_way', 'arrived', 'scan_kit', 'in_progress', 'payment_pending'])
            ->orderBy('date', 'asc')
            ->orderBy('slot', 'asc')
            ->take(10)
            ->get();

        $recentBookingsResource = \App\Http\Resources\Api\BookingResource::collection($recentBookings);

        $cashWallet = Wallet::where('holder_type', 'professional')
            ->where('holder_id', $professional->id)
            ->where('type', 'cash')
            ->first();
        $totalBalancePaise = $cashWallet ? $cashWallet->balance : 0;

        // Calculate Withdrawal Delay Logic (Hardened)
        $withdrawDelayDays = (int) (Setting::get('withdraw_delay_days') ?? 3);
        $cutoffDate = now()->subDays($withdrawDelayDays);

        // Sum earnings that are still in "cooldown" (completed after cutoff)
        $pendingEarningsPaise = Booking::where('professional_id', $professional->id)
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->where('completed_at', '>', $cutoffDate)
            ->get()
            ->sum(fn($b) => ($b->price - ($b->commission ?? 0)) * 100);

        $availableBalancePaise = (float) max(0, $totalBalancePaise - $pendingEarningsPaise);
        $availableBalancePaise = min($availableBalancePaise, (float) $totalBalancePaise); // Prevent overflow
        $pendingBalancePaise = (float) max(0, $totalBalancePaise - $availableBalancePaise);

        // Pending Requests (Only show if kits >= 5)
        $kitCount = \App\Models\KitOrder::where('professional_id', $professional->id)->sum('quantity');
        $pendingRequestsWait = 0;
        if ($kitCount >= 5) {
            $pendingRequestsWait = Booking::whereIn('status', ['unassigned', 'pending'])
                ->whereNull('professional_id')
                ->where('city', $professional->city)
                ->count();
        }
        // Fetch the active job specifically for the dashboard summary
        $activeJob = Booking::with(['customer', 'service', 'package', 'order'])
            ->where('professional_id', $professional->id)
            ->whereIn('status', ['accepted', 'on_the_way', 'arrived', 'scan_kit', 'in_progress', 'payment_pending'])
            ->latest()
            ->first();

        return $this->success([
            'status'            => true,
            'active_job'        => $activeJob ? new \App\Http\Resources\Api\BookingResource($activeJob) : null,
            'recent_bookings'   => $recentBookingsResource,
            'pending_requests'  => $pendingRequestsWait,
            'todays_earnings'   => (float) Booking::where('professional_id', $professional->id)
                                    ->where('date', $today)
                                    ->where('status', 'completed')
                                    ->sum('price'),
            'total_earnings'    => $professional->earnings,
            'total_orders'      => $professional->orders,
            'kit_count'         => \App\Models\KitOrder::where('professional_id', $professional->id)->sum('quantity'),
            'rating'            => $professional->rating,
            'is_online'         => $isOnlineAuthoritative,
            'total_balance'     => (float) ($totalBalancePaise / 100),
            'available_balance' => (float) ($availableBalancePaise / 100),
            'pending_balance'   => (float) ($pendingBalancePaise / 100),
            'withdraw_delay_days' => $withdrawDelayDays,
            'session_id'        => $professional->session_id,
            'shift_info' => [
                'start_time' => $shiftStart->toIso8601String(),
                'end_time' => $shiftEnd->toIso8601String(),
                'remaining_seconds' => max(0, $remainingSeconds),
                'is_active' => $isWithinShift,
                'progress' => round($shiftProgress, 4),
            ]
        ], 'Dashboard summary retrieved.');
    }

    /**
     * GET /api/professionals/active-job
     * Returns the current focused job for the professional workflow.
     */
    public function activeJob(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        // ✅ HARDENED FETCH (Covers all focused states)
        $job = Booking::with(['customer', 'service', 'package', 'order'])
            ->where('professional_id', $professional->id)
            ->whereIn('status', ['accepted', 'on_the_way', 'arrived', 'scan_kit', 'in_progress', 'payment_pending'])
            ->latest()
            ->first();

        // ✅ STANDARDIZED RESOURCE RESPONSE (Matches final architecture)
        return $this->success($job ? new \App\Http\Resources\Api\BookingResource($job) : null, 
            $job ? 'Active job retrieved.' : 'No active job found.');
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
            // Check global shift settings
            $shiftStartTime = Setting::get('shift_start_time', '09:00');
            $shiftDuration = (int) Setting::get('shift_duration', 480);
            $shiftStart = Carbon::today()->setTimeFromTimeString($shiftStartTime);
            $shiftEnd = $shiftStart->copy()->addMinutes($shiftDuration);
            if ($shiftEnd->lt($shiftStart)) $shiftEnd->addDay();

            if (!now()->between($shiftStart, $shiftEnd)) {
                return $this->error('You are currently outside global shift hours.', 403);
            }

            $kitCount = \App\Models\KitOrder::where('professional_id', $professional->id)->sum('quantity');
            if ($kitCount < 5) {
                return $this->error('Minimum 5 kits required to go online.', 422);
            }

            // Set new shift session
            $professional->session_id = (string) \Illuminate\Support\Str::uuid();
            $professional->shift_start_time = $shiftStart;
            $professional->shift_end_time = $shiftEnd;
        } else {
            // Going offline manually
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
        
        // Auto-offline check
        $shiftStartTime = Setting::get('shift_start_time', '09:00');
        $shiftDuration = (int) Setting::get('shift_duration', 480);
        $shiftStart = Carbon::today()->setTimeFromTimeString($shiftStartTime);
        $shiftEnd = $shiftStart->copy()->addMinutes($shiftDuration);
        if ($shiftEnd->lt($shiftStart)) $shiftEnd->addDay();

        if (!now()->between($shiftStart, $shiftEnd)) {
            if ($professional->is_online) {
                $professional->is_online = false;
                $professional->session_id = null;
                $professional->save();
            }
        }

        $professional->last_seen = now();
        $professional->save();

        $isOnlineAuthoritative = $professional->is_online && now()->between($shiftStart, $shiftEnd);

        return $this->success([
            'status'            => 'updated',
            'is_online'         => $isOnlineAuthoritative,
            'last_seen'         => $professional->last_seen,
            'remaining_seconds' => $isOnlineAuthoritative 
                                    ? max(0, now()->diffInSeconds($shiftEnd, false)) 
                                    : 0
        ], 'Heartbeat received.');
    }

    /**
     * Get the Top 3 Professional Leaderboard.
     */
    public function leaderboard(): JsonResponse
    {
        $topProfessionals = \App\Models\Professional::query()
            ->withCount(['bookings as completed_jobs_count' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->where('status', 'active')
            ->orderByDesc('completed_jobs_count')
            ->take(3)
            ->get();

        $data = $topProfessionals->values()->map(function ($pro, $index) {
            return [
                'id' => $pro->id,
                'name' => $pro->name,
                'role' => $pro->category ?? 'Professional',
                'image' => $pro->avatar ?? asset('assets/images/default-avatar.png'),
                'completed_jobs_count' => (int) $pro->completed_jobs_count,
                'rank' => $index + 1,
            ];
        });

        return $this->success($data, 'Leaderboard retrieved.');
    }
}
