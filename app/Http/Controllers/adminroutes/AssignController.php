<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Booking;
use App\Models\Professional;
use Illuminate\Http\Request;

class AssignController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['customer', 'service', 'professional'])
            ->where('status', '!=', 'Cancelled')
            ->get();
        $professionals = Professional::where('status', 'Active')->get();

        return view('assign.index', compact('bookings', 'professionals'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'professional_id' => 'required|exists:professionals,id',
        ]);

        $booking = Booking::findOrFail($request->booking_id);
        $booking->update([
            'professional_id' => $request->professional_id,
            'status' => 'Assigned',
        ]);

        return redirect()->route('assign.index')->with('success', 'Professional assigned successfully!');
    }

    public function autoAssign(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = Booking::with(['service'])->findOrFail($request->booking_id);

        // Auto-assign logic: Find active professionals in the same city
        $query = Professional::where('status', 'Active')
            ->where('city', $booking->city);

        // Optionally filter by category if service matches
        if ($booking->service && $booking->service->category_id) {
            // This is a bit simplified, ideally we'd have a mapping
            // But for now let's stick to city and rating
        }

        $professional = $query->orderBy('rating', 'desc')->first();

        if (!$professional) {
            return response()->json([
                'success' => false,
                'message' => "No available professionals found in {$booking->city}."
            ], 404);
        }

        $booking->update([
            'professional_id' => $professional->id,
            'status' => 'Assigned',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Successfully assigned {$professional->name} to booking #{$booking->id}.",
            'professional_name' => $professional->name
        ]);
    }
}
