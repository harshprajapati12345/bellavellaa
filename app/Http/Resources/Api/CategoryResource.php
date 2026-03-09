<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'type'        => $this->type,
            // has_groups is populated via withCount() in CategoryController@index
            // using ->withCount(['serviceGroups as active_service_groups_count' => ...])
            // Falls back to false if not loaded via withCount (e.g. in show/nested use)
            'has_groups'  => ($this->active_service_groups_count ?? 0) > 0,
            'image'       => $this->image ? url('storage/' . $this->image) : null,
            'description' => $this->description,
            'color'       => $this->color,
            'featured'    => (bool) $this->featured,
            'sort_order'  => $this->sort_order,
            'status'      => $this->status,
            'badge'       => $this->badge ?? null,
        ];
    }
}
