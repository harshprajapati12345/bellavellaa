<?php

namespace App\Http\Resources\Api;

use App\Support\MediaPathNormalizer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomepageResource extends JsonResource
{
    /**
     * Transform the homepage content into an array.
     * Image URL is generated here from relative path stored in DB.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'section'    => $this->section,
            'title'      => $this->title,
            'subtitle'   => $this->subtitle,
            'image'      => MediaPathNormalizer::url($this->image),
            'content'    => $this->content, // JSON/Array
            'status'     => $this->status,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
