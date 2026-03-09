<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'tag_label'   => $this->tag_label,
            'description' => $this->description,
            'image'       => $this->image ? url('storage/' . $this->image) : null,
            'sort_order'  => $this->sort_order,
            'status'      => $this->status,
        ];
    }
}
