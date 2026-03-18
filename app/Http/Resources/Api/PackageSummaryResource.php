<?php

namespace App\Http\Resources\Api;

use App\Services\ConfigurablePackageService;
use App\Support\MediaPathNormalizer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resolved = app(ConfigurablePackageService::class)
            ->buildResolvedConfiguration($this->resource);

        return [
            'id' => $this->id,
            'title' => $this->name,
            'name' => $this->name,
            'slug' => $this->slug,
            'image' => MediaPathNormalizer::url($this->image),
            'image_url' => MediaPathNormalizer::url($this->image),
            'short_description' => $this->short_description ?: $this->description,
            'description' => $this->description,
            'tag_label' => $this->tag_label,
            'package_mode' => $resolved['package_mode'],
            'base_price_threshold' => $resolved['base_price_threshold'],
            'discount_type' => $resolved['discount_type'],
            'discount_value' => $resolved['discount_value'],
            'price' => $resolved['totals']['final_total'],
            'selected_total' => $resolved['totals']['selected_total'],
            'display_price' => $resolved['totals']['final_total'],
            'original_price' => $resolved['totals']['original_total'],
            'discounted_price' => $resolved['totals']['discounted_total'],
            'discount_percentage' => $resolved['totals']['discount_percentage'],
            'discount_applied' => $resolved['totals']['discount_applied'],
            'duration_minutes' => $resolved['totals']['duration_minutes'],
            'rating' => $this->average_rating,
            'rating_avg' => $this->average_rating,
            'review_count' => $this->total_reviews,
            'total_reviews' => $this->total_reviews,
            'preview_items' => $resolved['preview_items'],
            'is_configurable' => (bool) $this->is_configurable,
            'quantity_allowed' => (bool) $this->quantity_allowed,
            'pricing_rule' => $this->pricing_rule,
            'duration_rule' => $this->duration_rule,
        ];
    }
}
