<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the booking into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'customer'        => new CustomerResource($this->whenLoaded('customer')),
            'service'         => new ServiceResource($this->whenLoaded('service')),
            'package'         => new PackageResource($this->whenLoaded('package')),
            'professional'    => new ProfessionalResource($this->whenLoaded('professional')),
            'booking_date'    => $this->booking_date,
            'booking_time'    => $this->booking_time,
            'status'          => $this->status,
            'total_amount'    => $this->total_amount,
            'address'         => $this->address,
            'notes'           => $this->notes,
            'created_at'      => $this->created_at?->toIso8601String(),
            'updated_at'      => $this->updated_at?->toIso8601String(),
        ];
    }
}
