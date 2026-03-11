<?php

namespace App\Http\Resources\Api;

use App\Support\MediaPathNormalizer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->display_price,
            'display_price' => $this->display_price,
            'original_price' => $this->original_price,
            'sale_price' => $this->sale_price,
            'is_discounted' => $this->is_discounted,
            'duration_minutes' => $this->resolved_duration_minutes,
            'image' => MediaPathNormalizer::url($this->image),
            'status' => $this->status,
            'is_default' => (bool) $this->is_default,
            'is_bookable' => $this->isBookable(),
            'bookable_type' => $this->isBookable() ? 'variant' : null,
            'sku' => $this->sku,
            'sort_order' => $this->sort_order,
        ];
    }
}
