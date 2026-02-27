<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'services_count' => $this->services_count,
            'bookings_count' => $this->bookings_count,
            'status' => $this->status,
            'featured' => (bool) $this->featured,
            'color' => $this->color,
            'image' => $this->image ? url($this->image) : null,
            'description' => $this->description,
        ];
    }
}
