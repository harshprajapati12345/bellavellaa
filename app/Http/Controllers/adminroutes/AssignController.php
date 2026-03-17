<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Booking;
use App\Models\Professional;
use App\Models\ProfessionalNotification;
use Illuminate\Http\Request;

use App\Services\FirebaseService;

class AssignController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }
    public function index()
    {
        $bookings = Booking::with(['customer', 'service', 'professional'])
            ->where('status', '!=', 'cancelled')
            ->get();
        $professionals = Professional::where('status', 'Active')
            ->where('is_online', 1)
            ->where('last_seen', '>=', now()->subMinutes(30))
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
                'status' => 'assigned',
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

            // Trigger Real-Time Push Notification (FCM)
            if ($booking->professional && $booking->professional->fcm_token) {
                $this->firebase->sendPushNotification(
                    $booking->professional->fcm_token,
                    'New Booking Assigned 🔔',
                    "A new service request for {$booking->service?->name} has been assigned to you.",
                    [
                        'type' => 'booking_assigned',
                        'booking_id' => (string)$booking->id,
                        'client_name' => $booking->customer?->name ?? 'Customer',
                        'service' => $booking->service?->name ?? 'Service',
                        'location' => $booking->address ?? 'Nearby',
                        'price' => (string)$booking->price,
                    ]
                );
            }

            // Real-time Push to Firestore (Uber-Style Job Popup trigger)
            $this->firebase->pushJobToFirestore([
                'professional_id' => $request->professional_id,
                'booking_id'      => $booking->id,
                'client_name'     => $booking->customer?->name ?? 'Customer',
                'service'         => $booking->service?->name ?? 'Service',
                'location'        => $booking->address ?? 'Nearby',
                'price'           => (string)$booking->price,
                'status'          => 'pending',
                'updated_at'      => time(),
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

        // Auto-assign logic: Find active, online professionals in the same city
        $query = Professional::where('status', 'Active')
            ->where('is_online', 1)
            ->where('last_seen', '>=', now()->subMinutes(30))
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
            'status' => 'assigned',
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

        // Trigger Real-Time Push Notification (FCM)
        if ($professional->fcm_token) {
            $this->firebase->sendPushNotification(
                $professional->fcm_token,
                'New Booking Assigned 🔔',
                "A new service request for {$booking->service?->name} has been assigned to you.",
                [
                    'type' => 'booking_assigned',
                    'booking_id' => (string)$booking->id,
                    'client_name' => $booking->customer?->name ?? 'Customer',
                    'service' => $booking->service?->name ?? 'Service',
                    'location' => $booking->address ?? 'Nearby',
                    'price' => (string)$booking->price,
                ]
            );
        }
        // Real-time Push to Firestore (Uber-Style Job Popup trigger)
        $this->firebase->pushJobToFirestore([
            'professional_id' => $professional->id,
            'booking_id'      => $booking->id,
            'client_name'     => $booking->customer?->name ?? 'Customer',
            'service'         => $booking->service?->name ?? 'Service',
            'location'        => $booking->address ?? 'Nearby',
            'price'           => (string)$booking->price,
            'status'          => 'pending',
            'updated_at'      => time(),
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
