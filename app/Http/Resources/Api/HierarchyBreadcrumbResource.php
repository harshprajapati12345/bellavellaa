<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HierarchyBreadcrumbResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $level = match (true) {
            $this->resource instanceof \App\Models\Category => 'category',
            $this->resource instanceof \App\Models\ServiceGroup => 'service_group',
            $this->resource instanceof \App\Models\ServiceType => 'service_type',
            $this->resource instanceof \App\Models\Service => 'service',
            $this->resource instanceof \App\Models\ServiceVariant => 'variant',
            default => 'unknown',
        };

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'level' => $level,
        ];
    }
}
