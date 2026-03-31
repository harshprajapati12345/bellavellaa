<?php

namespace App\Http\Resources\Api;

use App\Support\MediaPathNormalizer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => MediaPathNormalizer::url($this->image),
            'sort_order' => $this->sort_order,
            'status' => $this->status,
            'level' => 'service_type',
            'next_level' => 'service',
            'has_children' => ($this->services_count ?? 0) > 0 || $this->relationLoaded('services'),
        ];
    }
}
