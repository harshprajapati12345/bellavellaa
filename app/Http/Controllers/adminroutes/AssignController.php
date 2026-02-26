<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Booking;
use App\Models\Professional;
use Illuminate\Http\Request;

class AssignController extends Controller
{
    public function index()
    {
        $bookings = Booking::where('status', '!=', 'Cancelled')->get();
        $professionals = Professional::where('status', 'Active')->get();

        return view('assign.index', compact('bookings', 'professionals'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'booking_id'      => 'required|exists:bookings,id',
            'professional_id' => 'required|exists:professionals,id',
        ]);

        $booking = Booking::findOrFail($request->booking_id);
        $booking->update([
            'professional_id' => $request->professional_id,
            'status'          => 'Assigned',
        ]);

        return redirect()->route('assign.index')->with('success', 'Professional assigned successfully!');
    }
}
