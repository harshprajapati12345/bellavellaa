<?php

namespace App\Http\Resources\Api;

use App\Support\MediaPathNormalizer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'category_id' => $this->resolved_category?->id ?? $this->category_id,
            'service_group_id' => $this->resolved_service_group?->id ?? $this->service_group_id,
            'service_type_id' => $this->service_type_id,
            'short_description' => $this->short_description,
            'description' => $this->long_description ?: $this->description,
            'price' => $this->display_price,
            'display_price' => $this->display_price,
            'original_price' => $this->original_price,
            'sale_price' => $this->sale_price,
            'is_discounted' => $this->is_discounted,
            'bookings' => $this->bookings,
            'duration' => $this->resolved_duration_minutes,
            'duration_minutes' => $this->resolved_duration_minutes,
            'status' => $this->status,
            'featured' => (bool) $this->featured,
            'sort_order' => $this->sort_order,
            'image' => MediaPathNormalizer::url($this->image),
            'image_url' => MediaPathNormalizer::url($this->image),
            'has_variants' => (bool) $this->has_variants,
            'is_bookable' => $this->canBeBookedDirectly(),
            'allow_direct_booking_with_variants' => (bool) $this->allow_direct_booking_with_variants,
            'bookable_type' => $this->canBeBookedDirectly() ? 'service' : null,
            'average_rating' => $this->average_rating,
            'rating_avg' => $this->average_rating,
            'total_reviews' => $this->total_reviews,
            'review_count' => $this->total_reviews,
            'included_items' => $this->relationLoaded('includedItems') ? $this->includedItems->pluck('name')->values() : [],
            'addons' => $this->relationLoaded('addons') ? $this->addons->map(fn ($addon) => [
                'id' => $addon->id,
                'name' => $addon->name,
                'price' => $addon->price,
            ])->values() : [],
            'variants' => $this->relationLoaded('variants') ? ServiceVariantResource::collection($this->variants) : [],
        ];
    }
}
