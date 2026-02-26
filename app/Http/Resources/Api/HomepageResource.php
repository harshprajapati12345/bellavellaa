<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomepageResource extends JsonResource
{
    /**
     * Transform the homepage content into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'section'    => $this->section,
            'title'      => $this->title,
            'image'      => $this->image,
            'content'    => $this->content, // JSON/Array
            'status'     => $this->status,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
