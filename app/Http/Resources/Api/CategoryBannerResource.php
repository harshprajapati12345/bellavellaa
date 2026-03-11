<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CategoryBannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'image_url' => $this->image ? Storage::disk('public')->url($this->image) : null,
            'link_url' => $this->link_url,
            'banner_type' => $this->banner_type,
            'sort_order' => $this->sort_order,
        ];
    }
}
