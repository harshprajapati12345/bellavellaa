<?php

namespace App\Http\Resources\Api;

use App\Support\MediaPathNormalizer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $hasGroups = ($this->active_service_groups_count ?? 0) > 0 || $this->relationLoaded('serviceGroups');
        $hasDirectServices = ($this->active_direct_services_count ?? 0) > 0 || $this->relationLoaded('directServices');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'tagline' => $this->tagline,
            'image' => MediaPathNormalizer::url($this->image),
            'icon' => MediaPathNormalizer::url($this->icon),
            'description' => $this->description,
            'color' => $this->color,
            'featured' => (bool) $this->featured,
            'sort_order' => $this->sort_order,
            'status' => $this->status,
            'level' => 'category',
            'next_level' => $hasGroups ? 'service_group' : ($hasDirectServices ? 'service' : null),
            'has_service_groups' => $hasGroups,
            'has_children' => $hasGroups || $hasDirectServices,
            'badge' => $this->badge ?? null,
        ];
    }
}
