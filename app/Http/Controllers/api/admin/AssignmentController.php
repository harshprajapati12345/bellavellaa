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

        $professionals = Professional::useWritePdo()
            ->where('is_suspended', false)
            ->where('is_online', 1)
            ->whereNull('active_request_id')
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
        return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $booking = Booking::findOrFail($request->booking_id);
            $booking->loadMissing(['customer', 'service', 'package', 'order']);

            // 1. Get New Professional with pessimistic lock
            $newProfessional = Professional::lockForUpdate()->findOrFail($request->professional_id);

            // 2. Prevent Double Assignment
            if ($newProfessional->active_request_id && (int)$newProfessional->active_request_id !== (int)$booking->id) {
                return $this->error("Professional is already busy with another request (#{$newProfessional->active_request_id}).", 422);
            }

            // 3. Handle Transfer (Release old professional)
            $oldProfessionalId = $booking->professional_id;
            if ($oldProfessionalId && (int)$oldProfessionalId !== (int)$newProfessional->id) {
                $oldPro = Professional::find($oldProfessionalId);
                if ($oldPro && (int)$oldPro->active_request_id === (int)$booking->id) {
                    $oldPro->update(['active_request_id' => null]);
                    $oldPro->refresh();
                    broadcast(new \App\Events\ProfessionalStatusUpdated($oldPro))->toOthers();
                }
            }

            // 4. Update Booking
            $booking->fill([
                'professional_id' => $newProfessional->id,
                'status' => 'assigned',
            ]);
            $booking->markLifecycle('assigned_at');
            $booking->save();

            // 5. Set New Professional to BUSY
            $newProfessional->update([
                'active_request_id' => $booking->id,
                'last_assigned_at' => now(),
            ]);

            $payload = $this->bookingNotificationPayload($booking);

            // Create notification for the professional (DB)
            ProfessionalNotification::create([
                'professional_id' => $newProfessional->id,
                'type'            => 'booking_assigned',
                'title'           => 'New Booking Assigned!',
                'body'            => 'You have a new booking request from ' . ($booking->customer->name ?? 'Customer'),
                'data'            => $payload,
            ]);

            // Real-time Push to Firebase (Uber-Style Job UI)
            $this->firebase->pushJobToFirestore([
                'professional_id' => $newProfessional->id,
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
            $this->firebase->pushNotificationToFirestore($newProfessional->id, [
                'type'    => 'booking_assigned',
                'title'   => 'New Booking Assigned!',
                'body'    => 'You have a new booking request at ' . ($booking->address ?? 'Nearby'),
                'data'    => ['booking_id' => $booking->id],
                'status'  => 'unread',
            ]);

            // Send FCM Push for instant UI trigger in Flutter
            if ($newProfessional->fcm_token) {
                $this->firebase->sendPushNotification(
                    $newProfessional->fcm_token,
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
            $booking->refresh();
            broadcast(new JobUpdate($booking))->toOthers();
            
            $newProfessional->refresh();
            broadcast(new \App\Events\ProfessionalStatusUpdated($newProfessional))->toOthers();
        });

        return $this->success(
            new BookingResource($booking->load(['customer', 'service', 'package', 'professional'])),
            'Professional assigned successfully.'
        );
    }
}
