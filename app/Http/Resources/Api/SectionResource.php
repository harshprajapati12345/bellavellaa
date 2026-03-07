<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this['id'] ?? null,
            'type'       => $this['type'] ?? 'unknown',
            'title'      => $this['title'] ?? null,
            'subtitle'   => $this['subtitle'] ?? null,
            'sort_order' => $this['sort_order'] ?? 0,
            'items'      => $this['items'] ?? [],
        ];
    }
}
