<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $sellable = $this->sellable_item;

        return [
            'id' => $this->id,
            'customer_name' => $this->customer_display_name,
            'client_name' => $this->customer_display_name,
            'customer_phone' => $this->customer?->phone ?? $this->customer?->mobile ?? null,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'service' => new ServiceResource($this->whenLoaded('service')),
            'variant' => new ServiceVariantResource($this->whenLoaded('variant')),
            'package' => new PackageResource($this->whenLoaded('package')),
            'professional' => new ProfessionalResource($this->whenLoaded('professional')),
            'booking_date' => $this->date?->format('Y-m-d'),
            'booking_time' => $this->slot,
            'status' => $this->status,
            'current_step' => $this->current_step,
            'requested_at' => $this->created_at?->toIso8601String(),
            'assigned_at' => $this->assigned_at?->toIso8601String(),
            'accepted_at' => $this->accepted_at?->toIso8601String(),
            'on_the_way_at' => $this->on_the_way_at?->toIso8601String(),
            'arrived_at' => $this->arrived_at?->toIso8601String(),
            'started_at' => $this->service_started_at?->toIso8601String(),
            'service_started_at' => $this->service_started_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
            'cancel_reason_code' => $this->cancel_reason_code,
            'cancel_reason_note' => $this->cancel_reason_note,
            'can_track_professional' => $this->canTrackProfessional(),
            'can_reschedule' => $this->canReschedule(),
            'can_cancel' => $this->canCancel(),
            'reschedule_cutoff_at' => $this->rescheduleCutoffAt()?->toIso8601String(),
            'sellable_type' => $this->sellable_type,
            'sellable_id' => $this->sellable_id,
            'sellable_name' => $this->sellable_name,
            'service_name' => $this->service?->name ?? $this->service_name,
            'variant_name' => $this->variant?->name,
            'display_name' => $this->sellable_name,
            'total_amount' => $this->price,
            'display_price' => data_get($this->meta, 'totals.final_total')
                ?? optional($sellable)->display_price
                ?? optional($sellable)->price
                ?? $this->price,
            'duration_minutes' => data_get($this->meta, 'totals.duration_minutes')
                ?? optional($sellable)->resolved_duration_minutes
                ?? optional($sellable)->duration_minutes
                ?? optional($sellable)->duration
                ?? null,
            'address' => $this->order?->address,
            'payment_status' => $this->order?->payment_status,
            'city' => $this->city, // Fallback for address in model
            'lat' => $this->lat,
            'lng' => $this->lng,
            'slot' => $this->slot, // Used by Flutter model
            'date' => $this->date?->format('Y-m-d'), // Used by Flutter model
            'price' => (float) $this->price, // Used by Flutter model
            'notes' => $this->notes,
            'meta' => $this->meta,
            'package_snapshot' => data_get($this->meta, 'package_snapshot'),
            'package_configuration' => data_get($this->meta, 'configuration'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
