<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceVariantResource extends JsonResource
{
    /**
     * Transform the service variant into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'service_id'       => $this->service_id,
            'name'             => $this->name,
            'slug'             => $this->slug,
            'price'            => $this->price,
            'duration_minutes' => $this->duration_minutes,
            'image'            => $this->image ? (str_starts_with($this->image, 'http') ? $this->image : asset('storage/' . $this->image)) : null,
            'status'           => $this->status,
            'sort_order'       => $this->sort_order,
        ];
    }
}
