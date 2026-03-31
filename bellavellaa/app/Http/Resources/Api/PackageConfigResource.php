<?php

namespace App\Http\Resources\Api;

use App\Support\MediaPathNormalizer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageConfigResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $package = $this->resource['package'];
        $context = $this->resource['context'] ?? null;
        $resolved = $this->resource['resolved'];

        return [
            'id' => $package->id,
            'title' => $package->name,
            'name' => $package->name,
            'slug' => $package->slug,
            'image' => MediaPathNormalizer::url($package->image),
            'image_url' => MediaPathNormalizer::url($package->image),
            'short_description' => $package->short_description ?: $package->description,
            'description' => $package->description,
            'tag_label' => $package->tag_label,
            'package_mode' => $resolved['package_mode'],
            'context' => $context ? [
                'type' => $context['type'],
                'id' => $context['id'],
                'name' => $context['name'],
                'slug' => $context['slug'],
            ] : null,
            'is_configurable' => (bool) $package->is_configurable,
            'quantity_allowed' => (bool) $package->quantity_allowed,
            'pricing_rule' => $resolved['pricing_rule'],
            'duration_rule' => $resolved['duration_rule'],
            'base_price_threshold' => $resolved['base_price_threshold'],
            'discount_type' => $resolved['discount_type'],
            'discount_value' => $resolved['discount_value'],
            'groups' => $resolved['groups'],
            'totals' => $resolved['totals'],
            'preview_items' => $resolved['preview_items'],
        ];
    }
}
