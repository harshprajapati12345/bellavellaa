<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Booking;
use App\Models\Professional;
use App\Http\Resources\Api\BookingResource;
use App\Http\Resources\Api\ProfessionalResource;
use App\Http\Requests\Api\Admin\StoreAssignmentRequest;
use App\Models\ProfessionalNotification;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Events\JobUpdate;

class AssignmentController extends BaseController
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    protected function bookingNotificationPayload(Booking $booking): array
    {
        return [
            'booking_id' => $booking->id,
            'client_name' => $booking->customer->name ?? 'Customer',
            'service' => $booking->sellable_name ?? $booking->service?->name ?? $booking->package?->name ?? 'Service',
            'location' => $booking->order?->address ?? $booking->address ?? 'Nearby',
            'price' => (string) (data_get($booking->meta, 'totals.final_total')
                ?? data_get($booking->meta, 'totals.discounted_total')
                ?? $booking->price
                ?? 0),
        ];
    }
    /**
     * Display a listing of assignable bookings and active professionals.
     */
    public function index(): JsonResponse
    {
        $bookings = Booking::with(['customer', 'service', 'package'])
            ->whereNotIn('status', ['cancelled', 'completed', 'assigned'])
            ->latest()
            ->get();

        $professionals = Professional::where('status', 'Active')
            ->where('is_online', 1)
            ->where('last_seen', '>=', now()->subMinutes(30))
            ->latest()
            ->get();

        return $this->success([
            'bookings'      => BookingResource::collection($bookings),
            'professionals' => ProfessionalResource::collection($professionals),
        ], 'Assignable bookings and professionals retrieved.');
    }

    /**
     * Assign a professional to a booking.
     */
    public function store(StoreAssignmentRequest $request): JsonResponse
    {
        $booking = Booking::findOrFail($request->booking_id);
        $booking->loadMissing(['customer', 'service', 'package', 'order']);

        $booking->fill([
            'professional_id' => $request->professional_id,
            'status' => 'assigned',
        ]);
        $booking->markLifecycle('assigned_at');
        $booking->save();
        $payload = $this->bookingNotificationPayload($booking);

        // Create notification for the professional (DB)
        ProfessionalNotification::create([
            'professional_id' => $request->professional_id,
            'type'            => 'booking_assigned',
            'title'           => 'New Booking Assigned!',
            'body'            => 'You have a new booking request from ' . ($booking->customer->name ?? 'Customer'),
            'data'            => $payload,
        ]);

        // Real-time Push to Firebase (Uber-Style Job UI)
        $this->firebase->pushJobToFirestore([
            'professional_id' => $request->professional_id,
            'booking_id'      => $booking->id,
            'client_name'     => $payload['client_name'],
            'service'         => $payload['service'],
            'location'        => $payload['location'],
            'lat'             => $booking->lat,
            'lng'             => $booking->lng,
            'price'           => $payload['price'],
            'status'          => 'pending',
            'type'            => 'booking_assigned',
        ]);

        // Real-time Push to General Notifications (History & Redundancy)
        $this->firebase->pushNotificationToFirestore($request->professional_id, [
            'type'    => 'booking_assigned',
            'title'   => 'New Booking Assigned!',
            'body'    => 'You have a new booking request at ' . ($booking->address ?? 'Nearby'),
            'data'    => ['booking_id' => $booking->id],
            'status'  => 'unread',
        ]);

        // Send FCM Push for instant UI trigger in Flutter
        $professional = Professional::find($request->professional_id);
        if ($professional && $professional->fcm_token) {
            $this->firebase->sendPushNotification(
                $professional->fcm_token,
                'New Booking Assigned!',
                'You have a new booking request from ' . ($booking->customer->name ?? 'Customer'),
                [
                    'type' => 'booking_assigned',
                    'booking_id' => (string)$booking->id,
                    'client_name' => $payload['client_name'],
                    'service'     => $payload['service'],
                    'location'    => $payload['location'],
                    'price'       => $payload['price'],
                ]
            );
        }

        // Real-time WebSocket Dashboard Sync
        broadcast(new JobUpdate($booking));

        return $this->success(
            new BookingResource($booking->load(['customer', 'service', 'package', 'professional'])),
            'Professional assigned successfully.'
        );
    }
}
