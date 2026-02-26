<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the service into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'category'          => $this->category,
            'price'             => $this->price,
            'duration'          => $this->duration, // in minutes
            'status'            => $this->status,
            'featured'          => (bool) $this->featured,
            'image'             => $this->image,
            'description'       => $this->description,
            'bookings_count'    => $this->bookings,
            'created_at'        => $this->created_at?->toIso8601String(),
            'updated_at'        => $this->updated_at?->toIso8601String(),
        ];
    }
}
