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

    /**
     * POST /api/professionals/bookings/{id}/reject
     */
    public function reject(Request $request, $id): JsonResponse
    {
        $professional = $request->user('professional-api');

        $booking = Booking::findOrFail($id);

        if ((int)$booking->professional_id !== (int)$professional->id) {
            return $this->error('Unauthorized access.', 403);
        }

        $booking->update([
            'professional_id' => null,
            'status' => 'unassigned',
        ]);

        // Reset Firestore job status to idle
        $this->firebase->pushJobToFirestore([
            'professional_id' => $professional->id,
            'booking_id' => $booking->id,
            'status' => 'idle',
            'updated_at' => time(),
        ]);

        // Real-time WebSocket Dashboard Sync
        broadcast(new JobUpdate($booking->setAttribute('professional_id', $professional->id)));

        return $this->success(null, 'Booking request rejected.');
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

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to update booking status: ' . $e->getMessage(), 500);
        }

        return $this->success(new \App\Http\Resources\Api\BookingResource($booking), 'Booking status updated.');
    }
}
