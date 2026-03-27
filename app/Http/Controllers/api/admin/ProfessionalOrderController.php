<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Professional;
use App\Models\Booking;
use App\Models\Order;
use App\Http\Resources\Api\BookingResource; // Assuming this exists or will be generic
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfessionalOrderController extends BaseController
{
    /**
     * List all orders with optional area filtering.
     */
    public function index(Request $request, $id = null): JsonResponse
    {
        if ($request->filled('area')) {
            // Uncomment to debug if request is reaching backend
            // dd($request->area); 
        }

        $query = Order::with([
            'customer:id,name,area',
            'professional:id,name'
        ]);

        // If a specific professional ID is passed in the route, filter by it
        if ($id) {
            $query->where('professional_id', $id);
        }

        // ✅ Area filter via relationship (FINAL FIX)
        if ($request->filled('area')) {
            $area = strtolower(trim($request->area));

            $query->whereHas('customer', function ($q) use ($area) {
                $q->whereRaw('LOWER(area) LIKE ?', ["%$area%"]);
            });
        }

        // If a request comes from a professional detail page, we might still have a professional_id in the query
        if ($request->filled('professional_id')) {
            $query->where('professional_id', $request->professional_id);
        }

        $orders = $query->latest()->paginate($request->input('per_page', 20));

        return $this->success([
            'orders' => $orders
        ], 'Orders retrieved successfully.');
    }

    /**
     * Get booking history and earnings for a professional.
     */
    public function history(int $id): JsonResponse
    {
        $professional = Professional::findOrFail($id);

        $bookings = Booking::where('professional_id', $id)
            ->whereIn('status', ['completed', 'cancelled', 'rejected'])
            ->orderBy('date', 'desc')
            ->get();

        $stats = [
            'total_completed' => $bookings->where('status', 'completed')->count(),
            'total_cancelled' => $bookings->where('status', 'cancelled')->count(),
            'total_earning' => $bookings->where('status', 'completed')->sum('price'),
        ];

        return $this->success([
            'stats' => $stats,
            'history' => $bookings
        ], 'Booking history for ' . $professional->name);
    }
}
