<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * Transform the media into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'type'           => $this->type,
            'title'          => $this->title,
            'subtitle'       => $this->subtitle,
            'url'            => $this->url ? (filter_var($this->url, FILTER_VALIDATE_URL) ? $this->url : asset('storage/' . ltrim($this->url, '/'))) : null,
            'thumbnail'      => $this->thumbnail,
            'linked_section' => $this->linked_section,
            'target_page'    => $this->target_page,
            'status'         => $this->status,
            'order'          => $this->order,
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}
