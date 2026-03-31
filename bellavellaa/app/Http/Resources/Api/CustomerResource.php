<?php

namespace App\Http\Resources\Api;

use App\Support\MediaPathNormalizer;
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
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'mobile'        => $this->mobile,
            'avatar'        => MediaPathNormalizer::url($this->avatar),
            'date_of_birth' => $this->date_of_birth?->toDateString(),
            'status'        => $this->status,
            'joined'        => $this->joined?->toDateString(),
            'referral_code' => $this->referral_code,
            'created_at'    => $this->created_at?->toIso8601String(),
        ];
    }
}
