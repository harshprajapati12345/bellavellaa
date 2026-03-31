<?php

namespace App\Http\Resources\Api;

use App\Support\MediaPathNormalizer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $hasTypes = ($this->service_types_count ?? 0) > 0 || $this->relationLoaded('serviceTypes');
        $hasServices = ($this->services_count ?? 0) > 0 || $this->relationLoaded('services') || $this->relationLoaded('directServices');

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'tag_label' => $this->tag_label,
            'badge' => $this->badge,
            'description' => $this->description,
            'image' => MediaPathNormalizer::url($this->image),
            'sort_order' => $this->sort_order,
            'status' => $this->status,
            'level' => 'service_group',
            'next_level' => $hasTypes ? 'service_type' : ($hasServices ? 'service' : null),
            'has_children' => $hasTypes || $hasServices,
            'service_types' => $this->relationLoaded('serviceTypes')
                ? ServiceTypeResource::collection($this->serviceTypes)
                : [],
            'services' => $this->relationLoaded('services')
                ? ServiceResource::collection($this->services)
                : ($this->relationLoaded('directServices')
                    ? ServiceResource::collection($this->directServices)
                    : []),
        ];
    }
}
