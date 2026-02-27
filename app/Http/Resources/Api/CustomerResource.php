<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Controls exactly which customer fields are exposed to the API.
 *
 * Usage:  new CustomerResource($customer)
 * Collection: CustomerResource::collection($customers)
 */
class CustomerResource extends JsonResource
{
    /**
     * Transform the customer into a clean API shape.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'mobile'    => $this->mobile,
            'avatar'    => $this->avatar,
            'city'      => $this->city,
            'zip'       => $this->zip,
            'address'   => $this->address,
            'status'    => $this->status,
            'bookings'  => $this->bookings,
            'joined'    => $this->joined?->toDateString(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
