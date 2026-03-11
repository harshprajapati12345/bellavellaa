<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'image' => $this->image,
            'description' => $this->description,
            'service_groups' => ServiceGroupResource::collection($this->whenLoaded('serviceGroups')),
        ];
    }
}
