<?php

namespace App\Http\Controllers\Api\Professionals;

use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Events\JobUpdate;
use App\Http\Resources\Api\BookingResource;
use App\Models\Professional;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\DB;

class BookingController extends BaseController
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }
    /**
     * GET /api/professionals/bookings/requests
     * Get pending booking requests available to this professional
     */
    public function requests(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        // Only verified/approved professionals can see requests (allowing pending for testing/initial flow)
        if (strtolower($professional->verification) !== 'verified' &&
        strtolower($professional->verification) !== 'approved' &&
        strtolower($professional->verification) !== 'pending') {
            return $this->success([], 'Verify your account to see booking requests.');
        }

        // Fetch bookings assigned to this professional by the admin
        $bookings = Booking::with(['customer', 'service', 'package'])
            ->where('professional_id', $professional->id)
            ->where('status', 'assigned')
            ->latest('date')
            ->get();

        return $this->success(BookingResource::collection($bookings), 'Incoming requests retrieved.');
    }

    /**
     * GET /api/professionals/bookings
     * Get bookings assigned to this professional
     */
    public function index(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $bookings = Booking::where('professional_id', $professional->id)
            ->orderBy('date', 'desc')
            ->orderBy('slot', 'desc')
            ->get();

        return $this->success(BookingResource::collection($bookings), 'Bookings retrieved.');
    }

    /**
     * GET /api/professionals/bookings/{id}
     */
    public function show(Request $request, $id): JsonResponse
    {
        $professional = $request->user('professional-api');

        $booking = Booking::with(['customer', 'service', 'package'])->findOrFail($id);

        if ($booking->professional_id && (int)$booking->professional_id !== (int)$professional->id) {
            return $this->error('Unauthorized access.', 403);
        }

        return $this->success(new BookingResource($booking), 'Booking details retrieved.');
    }

    /**
     * POST /api/professionals/bookings/{id}/accept
     */
    public function accept(Request $request, $id): JsonResponse
    {
        $professional = $request->user('professional-api');

        if (strtolower($professional->verification) !== 'verified' &&
        strtolower($professional->verification) !== 'approved' &&
        strtolower($professional->verification) !== 'pending') {
            return $this->error('Only verified professionals can accept bookings. (Verification: ' . $professional->verification . ')', 403);
        }

        if (strtolower($professional->status) !== 'active') {
            return $this->error('Your account is currently suspended. (Status: ' . $professional->status . ')', 403);
        }

        $booking = Booking::find($id);

        if (!$booking) {
            return $this->error("Booking #{$id} not found.", 404);
        }

        // Use int cast to avoid strict int/string type mismatch
        if ((int)$booking->professional_id !== (int)$professional->id) {
            return $this->error("This booking is assigned to professional #{$booking->professional_id}, not #{$professional->id}.", 403);
        }

        if ($booking->status === 'accepted') {
            return $this->success(new BookingResource($booking), 'Booking already accepted.');
        }

        if ($booking->status !== 'assigned') {
            return $this->error("Cannot accept — booking status is '{$booking->status}' (expected 'assigned').", 400);
        }

        $booking->applyStatusTransition('accepted');

        // Reset Firestore job status to idle
        $this->firebase->pushJobToFirestore([
            'professional_id' => $professional->id,
            'booking_id' => $booking->id,
            'status' => 'idle',
            'updated_at' => time(),
        ]);

        // Real-time WebSocket Dashboard Sync
        broadcast(new JobUpdate($booking));

        return $this->success(new BookingResource($booking->fresh()), 'Booking accepted successfully.');
    }

    public function reject(Request $request, $id): JsonResponse
    {
        try {
            $professional = $request->user('professional-api');
            if (!$professional) {
                return $this->error('Unauthorized.', 401);
            }

            // ⌚ Timezone Awareness (IST)
            $today = now()->timezone('Asia/Kolkata')->toDateString();

            return DB::transaction(function () use ($professional, $today, $id) {
                // Atomic lock for professional record
                $pro = Professional::lockForUpdate()->find($professional->id);

                // ✅ Reset daily rejection count if it's a new day
                /** @var \Carbon\Carbon|null $lastRejectDate */
                $lastRejectDate = $pro->last_reject_date;
                $lastDate = $lastRejectDate ? $lastRejectDate->format('Y-m-d') : null;
                
                if ($lastDate !== $today) {
                    $pro->update([
                        'reject_count' => 0,
                        'last_reject_date' => $today,
                    ]);
                    
                    if (strtolower($pro->status) === 'suspended') {
                        $pro->update(['status' => 'active']);
                    }
                    
                    $pro->refresh();
                }

                // ❌ STEP 2: Already suspended check
                if (strtolower($pro->status) === 'suspended' || $pro->status !== 'active') {
                    return $this->error('Account suspended for today.', 403, [
                        'remaining_rejects' => 0,
                        'suspended' => true,
                        'status' => 'suspended'
                    ]);
                }

                // ❌ STEP 3: Block on 4th attempt (Check BEFORE incrementing)
                if ((int)$pro->reject_count >= 3) {
                    $pro->update([
                        'status' => 'suspended',
                    ]);
                    
                    return $this->error('Account suspended due to excessive rejections.', 403, [
                        'remaining_rejects' => 0,
                        'suspended' => true,
                        'status' => 'suspended'
                    ]);
                }

                // 🔒 Security: Ensure professional owns this booking
                $booking = Booking::where('id', $id)
                    ->where('professional_id', $pro->id)
                    ->first();

                if (!$booking) {
                    return $this->error("Unauthorized booking access.", 403);
                }

                // Prevent double reject/already processed (Check for fresh status)
                if ($booking->status !== 'assigned') {
                    return $this->error("Booking request is in '{$booking->status}' state.", 400);
                }

                // ✅ Increment atomically
                $pro->increment('reject_count');
                $pro->update(['last_reject_date' => $today]);
                
                $newCount = (int)$pro->reject_count;
                $remaining = max(0, 3 - $newCount);

                // Clear professional's BUSY status
                $pro->update(['active_request_id' => null]);

                // Update booking status
                $booking->update([
                    'status' => 'rejected',
                    'professional_id' => null,
                ]);

                // ✅ Unified Status Broadcast (Inside Transaction)
                $pro->refresh();
                broadcast(new \App\Events\ProfessionalStatusUpdated($pro))->toOthers();

                return $this->success([
                    'remaining_rejects' => $remaining,
                    'suspended' => false,
                    'reject_count' => $newCount,
                    'status' => 'active',
                    'message' => 'Booking rejected successfully.'
                ], 'Booking request rejected.');
            });

        } catch (\Exception $e) {
            \Log::error("Reject API Error: " . $e->getMessage());
            return $this->error("Failed to process rejection: " . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/professionals/bookings/{id}/status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $professional = $request->user('professional-api');

        $validated = $request->validate([
            'status' => 'required|in:accepted,on_the_way,arrived,in_progress,payment_pending,completed,cancelled,rejected',
        ]);

        $booking = Booking::findOrFail($id);

        if ((int)$booking->professional_id !== (int)$professional->id) {
            return $this->error('Unauthorized access.', 403);
        }

        $originalStatus = $booking->status;
        DB::beginTransaction();
        try {
            $booking->applyStatusTransition($validated['status']);

            // Real-time WebSocket Dashboard Sync
            broadcast(new JobUpdate($booking));

            // If completed, update earnings + orders count using centralized service
            if ($validated['status'] === 'completed' && $originalStatus !== 'completed') {
                \App\Services\BookingService::completeJob($booking);
            }

            // Reset BUSY status for terminal states
            if (in_array($validated['status'], ['completed', 'cancelled', 'rejected'])) {
                $professional->update(['active_request_id' => null]);
                $professional->refresh();
                broadcast(new \App\Events\ProfessionalStatusUpdated($professional))->toOthers();
            }

            DB::commit();

            return $this->success(new \App\Http\Resources\Api\BookingResource($booking), 'Booking status updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to update booking status: ' . $e->getMessage(), 500);
        }
    }
}
