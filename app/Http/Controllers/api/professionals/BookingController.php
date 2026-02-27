<?php

namespace App\Http\Controllers\Api\Professionals;

use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        // Available requests: Unassigned bookings in the professional's city
        // (In a real app, you might also filter by category or services)
        $query = Booking::whereIn('status', ['Unassigned', 'Pending'])
            ->whereNull('professional_id');
            
        if ($professional->city) {
            $query->where('city', $professional->city);
        }

        $bookings = $query->latest('date')->get();

        return $this->success($bookings, 'Booking requests retrieved.');
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
            return $this->error('Only verified professionals can accept bookings.', 403);
        }

        if ($professional->status !== 'Active') {
            return $this->error('Your account is currently suspended.', 403);
        }

        $booking = Booking::findOrFail($id);

        if ($booking->professional_id) {
            return $this->error('This booking has already been assigned to another professional.', 400);
        }

        $booking->update([
            'professional_id' => $professional->id,
            'professional_name' => $professional->name,
            'status' => 'Assigned',
        ]);

        return $this->success($booking, 'Booking accepted successfully.');
    }

    /**
     * POST /api/professionals/bookings/{id}/reject
     */
    public function reject(Request $request, $id): JsonResponse
    {
        // For a pool-based system, reject usually just hides it from the professional.
        // We'll return success to let the app remove it from the UI.
        return $this->success(null, 'Booking request dismissed.');
    }

    /**
     * POST /api/professionals/bookings/{id}/status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $professional = $request->user('professional-api');
        
        $validated = $request->validate([
            'status' => 'required|in:Assigned,Started,In Progress,Completed,Cancelled',
        ]);

        $booking = Booking::findOrFail($id);

        if ($booking->professional_id !== $professional->id) {
            return $this->error('Unauthorized access.', 403);
        }

        $originalStatus = $booking->status;
        $booking->update(['status' => $validated['status']]);

        // If completed, update earnings + orders count on professional
        if ($validated['status'] === 'Completed' && $originalStatus !== 'Completed') {
            $commissionAmt = ($booking->price * $professional->commission) / 100;
            $earnings = $booking->price - $commissionAmt;
            
            $professional->increment('orders');
            $professional->increment('earnings', $earnings);
        }

        return $this->success($booking, 'Booking status updated.');
    }
}
