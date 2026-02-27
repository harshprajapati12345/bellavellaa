<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Professional;
use App\Models\Booking;
use App\Http\Resources\Api\BookingResource; // Assuming this exists or will be generic
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfessionalOrderController extends BaseController
{
    /**
     * List active and upcoming orders for a professional.
     */
    public function index(int $id): JsonResponse
    {
        $professional = Professional::findOrFail($id);
        
        $bookings = Booking::where('professional_id', $id)
            ->whereIn('status', ['Assigned', 'Started', 'In Progress'])
            ->orderBy('date', 'asc')
            ->orderBy('slot', 'asc')
            ->get();

        return $this->success($bookings, 'Active orders for ' . $professional->name);
    }

    /**
     * Get booking history and earnings for a professional.
     */
    public function history(int $id): JsonResponse
    {
        $professional = Professional::findOrFail($id);
        
        $bookings = Booking::where('professional_id', $id)
            ->whereIn('status', ['Completed', 'Cancelled'])
            ->orderBy('date', 'desc')
            ->get();

        $stats = [
            'total_completed' => $bookings->where('status', 'Completed')->count(),
            'total_cancelled' => $bookings->where('status', 'Cancelled')->count(),
            'total_earning'   => $bookings->where('status', 'Completed')->sum('price'),
        ];

        return $this->success([
            'stats'   => $stats,
            'history' => $bookings
        ], 'Booking history for ' . $professional->name);
    }
}
