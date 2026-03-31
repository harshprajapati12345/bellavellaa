<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\MediaPathNormalizer;

class PackageResource extends JsonResource
{
    /**
     * Transform the package into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'title'             => $this->name,
            'slug'              => $this->slug,
            'category_id'       => $this->category_id,
            // Services via package_service pivot — loaded with ->with('services')
            'services'          => $this->whenLoaded('services', fn() =>
                $this->services->map(fn($s) => [
                    'id'    => $s->id,
                    'name'  => $s->name,
                    'price' => $s->price,
                ])
            ),
            'price'             => $this->price,
            'discount'          => $this->discount,
            'duration'          => $this->duration,
            'duration_minutes'  => $this->duration,
            'description'       => $this->description,
            'short_description' => $this->short_description ?: $this->description,
            'tag_label'         => $this->tag_label,
            'package_mode'      => $this->package_mode,
            'base_price_threshold' => $this->base_price_threshold,
            'discount_type'     => $this->discount_type,
            'discount_value'    => $this->discount_value,
            'desc_title'        => $this->desc_title,
            'desc_image'        => $this->desc_image,
            'aftercare_content' => $this->aftercare_content,
            'aftercare_image'   => $this->aftercare_image,
            'status'            => $this->status,
            'featured'          => (bool) $this->featured,
            'is_configurable'   => (bool) $this->is_configurable,
            'quantity_allowed'  => (bool) $this->quantity_allowed,
            'pricing_rule'      => $this->pricing_rule,
            'duration_rule'     => $this->duration_rule,
            'sort_order'        => $this->sort_order,
            'image'             => MediaPathNormalizer::url($this->image),
            'image_url'         => MediaPathNormalizer::url($this->image),
            'rating_avg'        => $this->average_rating,
            'review_count'      => $this->total_reviews,
            'created_at'        => $this->created_at?->toIso8601String(),
            'updated_at'        => $this->updated_at?->toIso8601String(),
        ];
    }
}
