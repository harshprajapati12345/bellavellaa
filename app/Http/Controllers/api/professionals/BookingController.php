<?php

namespace App\Http\Controllers\Api\Professionals;

use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Events\JobUpdate;

class BookingController extends BaseController
{
    /**
     * GET /api/professionals/bookings/requests
     * Get pending booking requests available to this professional
     */
    public function requests(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        // Only verified professionals can see requests
        if ($professional->verification !== 'Verified') {
            return $this->success([], 'Verify your account to see booking requests.');
        }

        // Fetch bookings assigned to this professional by the admin
        $bookings = Booking::with(['customer', 'service', 'package'])
            ->where('professional_id', $professional->id)
            ->where('status', 'assigned')
            ->latest('date')
            ->get();

        return $this->success($bookings, 'Incoming requests retrieved.');
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

        return $this->success($bookings, 'Bookings retrieved.');
    }

    /**
     * GET /api/professionals/bookings/{id}
     */
    public function show(Request $request, $id): JsonResponse
    {
        $professional = $request->user('professional-api');

        $booking = Booking::findOrFail($id);

        if ($booking->professional_id && $booking->professional_id !== $professional->id) {
            return $this->error('Unauthorized access.', 403);
        }

        return $this->success($booking, 'Booking details retrieved.');
    }

    /**
     * POST /api/professionals/bookings/{id}/accept
     */
    public function accept(Request $request, $id): JsonResponse
    {
        $professional = $request->user('professional-api');

        if ($professional->verification !== 'Verified') {
            return $this->error('Only verified professionals can accept bookings. (Verification: ' . $professional->verification . ')', 403);
        }

        if ($professional->status !== 'Active') {
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

        if ($booking->status !== 'assigned') {
            return $this->error("Cannot accept — booking status is '{$booking->status}' (expected 'assigned').", 400);
        }

        $booking->update(['status' => 'accepted']);

        // Real-time WebSocket Dashboard Sync
        broadcast(new JobUpdate($booking));

        // customer_phone, service_name, customer_name are denormalized columns on bookings
        return $this->success($booking->fresh(), 'Booking accepted successfully.');
    }

    /**
     * POST /api/professionals/bookings/{id}/reject
     */
    public function reject(Request $request, $id): JsonResponse
    {
        $professional = $request->user('professional-api');
        
        $booking = Booking::findOrFail($id);

        if ($booking->professional_id !== $professional->id) {
            return $this->error('Unauthorized access.', 403);
        }

        $booking->update([
            'professional_id' => null,
            'status' => 'pending' // Revert to pending so admin can reassign
        ]);

        // Real-time WebSocket Dashboard Sync (to notify current pro that job is cleared)
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
            'status' => 'required|in:assigned,started,in_progress,completed,cancelled',
        ]);

        $booking = Booking::findOrFail($id);

        if ($booking->professional_id !== $professional->id) {
            return $this->error('Unauthorized access.', 403);
        }

        $originalStatus = $booking->status;
        $booking->update(['status' => $validated['status']]);

        // Real-time WebSocket Dashboard Sync
        broadcast(new JobUpdate($booking));

        // If completed, update earnings + orders count on professional
        if ($validated['status'] === 'completed' && $originalStatus !== 'completed') {
            $commissionAmt = ($booking->price * $professional->commission) / 100;
            $earnings = $booking->price - $commissionAmt;
            
            $professional->increment('orders');
            $professional->increment('earnings', $earnings);
        }

        return $this->success($booking, 'Booking status updated.');
    }
}
