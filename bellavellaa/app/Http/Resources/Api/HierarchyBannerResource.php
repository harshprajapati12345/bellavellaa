<?php

namespace App\Http\Resources\Api;

use App\Support\MediaPathNormalizer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HierarchyBannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title ?? '',
            'subtitle' => $this->subtitle,
            'placement_type' => $this->placement_type,
            'media_type' => $this->media_type,
            'media_path' => $this->media_path,
            'media_url' => MediaPathNormalizer::url($this->media_path),
            'thumbnail_path' => $this->thumbnail_path,
            'thumbnail_url' => MediaPathNormalizer::url($this->thumbnail_path),
            'target_type' => $this->target_type,
            'target_id' => $this->target_id,
            'action_link' => $this->action_link,
            'button_text' => $this->button_text,
            'sort_order' => $this->sort_order,
            'status' => $this->status,
        ];
    }
}
