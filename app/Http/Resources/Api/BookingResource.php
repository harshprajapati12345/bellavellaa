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
            'customer_name' => $this->customer?->name ?? 'Customer',
            'client_name' => $this->customer?->name ?? 'Customer',
            'customer_phone' => $this->customer?->phone ?? $this->customer?->mobile ?? null,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'service' => new ServiceResource($this->whenLoaded('service')),
            'variant' => new ServiceVariantResource($this->whenLoaded('variant')),
            'package' => new PackageResource($this->whenLoaded('package')),
            'professional' => new ProfessionalResource($this->whenLoaded('professional')),
            'booking_date' => $this->date,
            'booking_time' => $this->slot,
            'status' => $this->status,
            'sellable_type' => $this->sellable_type,
            'sellable_id' => $this->sellable_id,
            'sellable_name' => $this->sellable_name,
            'service_name' => $this->service?->name ?? $this->service_name,
            'variant_name' => $this->variant?->name,
            'display_name' => $this->sellable_name,
            'total_amount' => $this->price,
            'display_price' => data_get($this->meta, 'totals.final_total')
                ?? $sellable->display_price
                ?? $sellable->price
                ?? $this->price,
            'duration_minutes' => data_get($this->meta, 'totals.duration_minutes')
                ?? $sellable->resolved_duration_minutes
                ?? $sellable->duration_minutes
                ?? $sellable->duration
                ?? null,
            'address' => $this->order?->address,
            'notes' => $this->notes,
            'meta' => $this->meta,
            'package_snapshot' => data_get($this->meta, 'package_snapshot'),
            'package_configuration' => data_get($this->meta, 'configuration'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
