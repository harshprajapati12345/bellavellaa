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
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'price' => $this->price,
            'duration' => $this->duration, // in minutes
            'status' => $this->status,
            'featured' => (bool) $this->featured,
            'image' => $this->image,
            'description' => $this->description,
            'bookings_count' => $this->bookings,
            'average_rating' => $this->average_rating,
            'total_reviews' => $this->total_reviews,
        ];
    }
}
