<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Booking;
use App\Models\Professional;
use App\Models\ProfessionalNotification;
use Illuminate\Http\Request;

class AssignController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['customer', 'service', 'professional'])
            ->where('status', '!=', 'Cancelled')
            ->get();
        $professionals = Professional::where('status', 'Active')
            ->where('kit_purchased', 1)
            ->where('last_seen', '>=', now()->subMinutes(2))
            ->get();

        return view('assign.index', compact('bookings', 'professionals'));
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'booking_id' => 'required|exists:bookings,id',
                'professional_id' => 'required|exists:professionals,id',
            ]);

            $booking = Booking::findOrFail($request->booking_id);
            $booking->update([
                'professional_id' => $request->professional_id,
                'status' => 'Assigned',
            ]);

            // Create notification for the professional
            ProfessionalNotification::create([
                'professional_id' => $request->professional_id,
                'type' => 'booking_assigned',
                'title' => 'New Booking Assigned 🔔',
                'body' => "A new service request for {$booking->service?->name} has been assigned to you.",
                'data' => [
                    'booking_id'  => $booking->id,
                    'client_name' => $booking->customer?->name ?? 'Customer',
                    'service'     => $booking->service?->name ?? 'Service',
                    'location'    => $booking->address ?? 'Nearby',
                    'price'       => $booking->price,
                ],
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Professional assigned successfully!',
                    'booking_id' => $booking->id
                ]);
            }

            return redirect()->route('assign.index')->with('success', 'Professional assigned successfully!');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to assign professional: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
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

        // Create notification for the professional
        ProfessionalNotification::create([
            'professional_id' => $professional->id,
            'type'            => 'booking_assigned',
            'title'           => 'New Booking Assigned 🔔',
            'body'            => "A new service request for {$booking->service?->name} has been assigned to you.",
            'data'            => [
                'booking_id'  => $booking->id,
                'client_name' => $booking->customer?->name ?? 'Customer',
                'service'     => $booking->service?->name ?? 'Service',
                'location'    => $booking->address ?? 'Nearby',
                'price'       => $booking->price,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => "Successfully assigned {$professional->name} to booking #{$booking->id}.",
            'professional_name' => $professional->name
        ]);
    }

    public function show($id)
    {
        $booking = Booking::with(['customer', 'service', 'package', 'professional'])->findOrFail($id);

        return response()->json([
            'id'                  => $booking->id,
            'status'              => $booking->status,
            'city'                => $booking->city ?? '—',
            'date'                => $booking->date ? \Carbon\Carbon::parse($booking->date)->format('d M Y') : '—',
            'slot'                => $booking->slot ?? '—',
            'customer_name'       => $booking->customer?->name ?? $booking->customer_name ?? 'Guest',
            'customer_avatar'     => $booking->customer?->avatar ?? null,
            'customer_phone'      => $booking->customer?->phone ?? '—',
            'service_name'        => $booking->service?->name ?? $booking->service_name ?? '—',
            'package_name'        => $booking->package?->name ?? $booking->package_name ?? null,
            'professional_name'   => $booking->professional?->name ?? $booking->professional_name ?? 'Not Assigned',
            'professional_avatar' => $booking->professional?->avatar ?? null,
            'price'               => $booking->price ?? $booking->service?->price ?? '0.00',
            'payment_status'      => $booking->payment_status ?? 'Pending',
        ]);
    }
}
