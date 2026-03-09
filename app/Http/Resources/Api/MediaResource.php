<?php

namespace App\Http\Resources\Api;

use App\Support\MediaPathNormalizer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * Transform the media into an array.
     * URL generation happens ONLY here — DB stores relative paths.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'type'           => $this->type,
            'title'          => $this->title,
            'subtitle'       => $this->subtitle,
            'url'            => MediaPathNormalizer::url($this->url),
            'thumbnail'      => MediaPathNormalizer::url($this->thumbnail),
            'homepage_content_id' => $this->homepage_content_id,
            'section'        => $this->homepageContent?->section,
            'target_page'    => $this->target_page,
            'status'         => $this->status,
            'order'          => $this->order,
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}
