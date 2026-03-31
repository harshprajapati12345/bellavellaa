<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this['id'] ?? null,
            'type'         => $this['type'] ?? 'unknown',
            'key'          => $this['key'] ?? $this['name'] ?? null,
            'name'         => $this['name'] ?? null,
            'title'        => $this['title'] ?? null,
            'subtitle'     => $this['subtitle'] ?? null,
            'media_type'   => $this['media_type'] ?? 'banner',
            'content_type' => $this['content_type'] ?? 'dynamic',
            'data_source'  => $this['data_source'] ?? null,
            'description'  => $this['description'] ?? null,
            'btn_text'     => $this['btn_text'] ?? null,
            'btn_link'     => $this['btn_link'] ?? null,
            'sort_order'   => $this['sort_order'] ?? 0,
            'items'        => $this['items'] ?? [],
        ];
    }
}
