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
            'id'               => $this->id,
            'name'             => $this->name,
            'slug'             => $this->slug,
            'category_id'      => $this->category_id,
            'service_group_id' => $this->service_group_id,
            'price'            => $this->price,
            'duration'         => $this->duration,
            'status'           => $this->status,
            'featured'         => (bool) $this->featured,
            'sort_order'       => $this->sort_order,
            'image'            => $this->image,
            'description'      => $this->description,
            'desc_title'       => $this->desc_title,
            'has_variants'     => (bool) $this->has_variants,
            'service_types'    => $this->service_types,
            'average_rating'   => $this->average_rating,
            'total_reviews'    => $this->total_reviews,
        ];
    }
}
