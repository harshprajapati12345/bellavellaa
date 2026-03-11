<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
        // Build avatar URL using the actual request origin so it works with
        // both `php artisan serve` (127.0.0.1:8000) and production deployments.
        $avatarUrl = null;
        if ($this->avatar) {
            $avatarUrl = $request->root() . '/storage/' . ltrim($this->avatar, '/');
        }

        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'mobile'        => $this->mobile,
            'avatar'        => $avatarUrl,
            'date_of_birth' => $this->date_of_birth?->toDateString(),
            'status'        => $this->status,
            'joined'        => $this->joined?->toDateString(),
            'referral_code' => $this->referral_code,
            'created_at'    => $this->created_at?->toIso8601String(),
        ];
    }
}
