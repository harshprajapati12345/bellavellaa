<?php

namespace App\Http\Controllers\Api\Professionals;

use App\Models\Booking;
use App\Models\Wallet;
use App\Models\Setting;
use App\Models\Professional;
use Illuminate\Support\Facades\DB;
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
        
        // ─── Robust Shift Management ──────────────────────────────────
        $this->syncShiftState($professional);
        $remainingSeconds = $professional->remaining_seconds_today;

        // Visual Progress Calculation (for Dashboard UI)
        $quotaInMinutes = $professional->shift_duration ?: Setting::get('shift_duration', 480);
        $totalQuotaSeconds = $quotaInMinutes * 60;
        $elapsedSeconds = $totalQuotaSeconds - $remainingSeconds;
        $shiftProgress = $totalQuotaSeconds > 0 ? ($elapsedSeconds / $totalQuotaSeconds) : 0;
        $shiftProgress = max(0, min(1, $shiftProgress)); // Clamp

        $recentBookings = Booking::with(['customer', 'service', 'package'])
            ->where('professional_id', $professional->id)
            ->whereIn('status', ['accepted', 'on_the_way', 'arrived', 'scan_kit', 'in_progress', 'payment_pending'])
            ->orderBy('date', 'asc')
            ->orderBy('slot', 'asc')
            ->take(10)
            ->get();

        $recentBookingsResource = \App\Http\Resources\Api\BookingResource::collection($recentBookings);

        $cashWallet = $professional->cashWallet()->first();
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
            'is_online'         => (bool) $professional->is_online,
            'total_balance'     => (float) ($totalBalancePaise / 100),
            'available_balance' => (float) ($availableBalancePaise / 100),
            'pending_balance'   => (float) ($pendingBalancePaise / 100),
            'withdraw_delay_days' => $withdrawDelayDays,
            'session_id'        => $professional->session_id,
            'shift_info' => [
                'remaining_seconds' => (int) $remainingSeconds,
                'is_active' => $remainingSeconds > 0,
                'progress' => round($shiftProgress, 4),
                'online_started_at' => $professional->last_online_at ? $professional->last_online_at->toIso8601String() : null,
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
            // Check account status first
            if ($professional->status !== 'active') {
                return $this->error('Account ' . $professional->status, 403);
            }

            // [NEW] Exhaustion Block: Prevent online if quota already used up
            if ($professional->remaining_seconds_today <= 0) {
                return $this->error('You have exhausted your daily shift quota.', 403);
            }

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

            DB::transaction(function() use ($professional) {
                $p = Professional::lockForUpdate()->find($professional->id);
                $p->session_id = (string) \Illuminate\Support\Str::uuid();
                $p->last_online_at = now();
                $p->is_online = true;
                $p->save();
            });
        } else {
            // Going offline manually
            DB::transaction(function() use ($professional) {
                $p = Professional::lockForUpdate()->find($professional->id);
                if ($p->is_online && $p->last_online_at) {
                    $elapsed = now()->diffInSeconds($p->last_online_at);
                    $p->accumulated_seconds_today += $elapsed;
                    $p->accumulated_seconds_today = min($p->accumulated_seconds_today, $p->getQuotaSeconds());
                }
                $p->last_online_at = null;
                $p->session_id = null;
                $p->is_online = false;
                $p->save();
            });
        }

        try {
            broadcast(new \App\Events\StatusUpdated($professional));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Broadcast failed: " . $e->getMessage());
        }
        
        \Illuminate\Support\Facades\Log::info("EVENT FIRED", [
          'id' => $professional->id,
          'status' => $professional->status
        ]);

        return $this->success([
            'status'         => $professional->status,
            'is_online'      => (bool) $professional->is_online,
            'availability_status' => $professional->availability_status,
            'remaining_seconds' => $professional->remaining_seconds_today,
            'online_started_at' => $professional->last_online_at ? $professional->last_online_at->toIso8601String() : null,
        ], 'Availability status updated.');
    }

    /**
     * POST /api/professional/update-online-status
     */
    public function updateOnlineStatus(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');
        
        // Update last_seen FIRST for accurate recovery window
        $professional->last_seen = now();
        $professional->save();

        $this->syncShiftState($professional);

        // Fetch fresh state after sync (which might have force-offline'd the pro)
        $professional->refresh();

        if ($professional->is_online && ($professional->remaining_seconds_today <= 0 || $professional->status !== 'active')) {
            DB::transaction(function() use ($professional) {
                $p = Professional::lockForUpdate()->find($professional->id);
                if ($p->is_online) {
                     if ($p->last_online_at) {
                        $elapsed = now()->diffInSeconds($p->last_online_at);
                        $p->accumulated_seconds_today += $elapsed;
                        $p->accumulated_seconds_today = min($p->accumulated_seconds_today, $p->getQuotaSeconds());
                    }
                    $p->is_online = false;
                    $p->last_online_at = null;
                    $p->session_id = null;
                    $p->save();
                }
            });
        }

        return $this->success([
            'status'            => 'updated',
            'is_online'         => (bool) $professional->is_online,
            'last_seen'         => $professional->last_seen,
            'remaining_seconds' => $professional->remaining_seconds_today
        ], 'Heartbeat received.');
    }

    /**
     * ─── Shift State Synchronization ──────────────────────────────────
     * Centralized logic to:
     * 1. Handle 6 AM daily reset of accumulated time.
     * 2. Close "stale" sessions if the pro was online but missed heartbeats.
     * 3. Sync accumulated seconds to DB when sessions end.
     */
    private function syncShiftState($professional): void
    {
        DB::transaction(function() use ($professional) {
            $p = Professional::lockForUpdate()->find($professional->id);
            $now = now();
            $quota = $p->getQuotaSeconds();
            $resetThreshold = $p->getResetThreshold();
            $grace = 120; // 2 minute grace period for disconnects

            // 1. Daily Reset with Atomic Split
            if (!$p->last_reset_at || $p->last_reset_at->lt($resetThreshold)) {
                if ($p->is_online && $p->last_online_at && $p->last_online_at->lt($resetThreshold)) {
                    // Close the previous day's session exactly at 6 AM
                    $effectiveEnd = min($resetThreshold, ($p->last_seen ? $p->last_seen->addSeconds($grace) : $resetThreshold));
                    $duration = max(0, $effectiveEnd->diffInSeconds($p->last_online_at));
                    
                    $p->accumulated_seconds_today += $duration;
                    $p->accumulated_seconds_today = min($p->accumulated_seconds_today, $quota);
                    
                    // Note: Here we'd typically log the finalized daily time to a reports table.
                }
                
                // Reset for the new cycle (starts fresh at 0)
                $p->accumulated_seconds_today = 0;
                $p->last_reset_at = $now;
                if ($p->is_online) {
                    $p->last_online_at = $resetThreshold; // New session starts at exactly 6:00:00 AM
                }
                $p->save();
            }

            // 2. Stale Session Recovery (Missed Heartbeats)
            if ($p->is_online && $p->last_online_at && $p->last_seen) {
                if ($now->diffInMinutes($p->last_seen) > 3) {
                    $effectiveEnd = min($now, $p->last_seen->addSeconds($grace));
                    if ($effectiveEnd->gt($p->last_online_at)) {
                        $duration = $effectiveEnd->diffInSeconds($p->last_online_at);
                        $p->accumulated_seconds_today += $duration;
                        $p->accumulated_seconds_today = min($p->accumulated_seconds_today, $quota);
                        $p->last_online_at = $effectiveEnd; // 🛡️ Double-recovery Guard
                    }
                    
                    $p->is_online = false;
                    $p->last_online_at = null;
                    $p->session_id = null;
                    $p->save();
                    
                    \Illuminate\Support\Facades\Log::info("Professional #{$p->id} force-offline (stale). Mode: Atomic Recovery.");
                }
            }
        });
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
                'image' => $pro->avatar, // Uses new accessor
                'completed_jobs_count' => (int) $pro->completed_jobs_count,
                'updated_at' => $pro->updated_at?->toIso8601String(),
                'rank' => $index + 1,
            ];
        });

        return $this->success($data, 'Leaderboard retrieved.');
    }
}
