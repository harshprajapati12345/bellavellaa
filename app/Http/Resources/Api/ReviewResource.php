<?php

namespace App\Http\Resources\Api;

use App\Support\MediaPathNormalizer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the review into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'customer_id'     => $this->customer_id,
            'customer_name'   => $this->customer_name ?? $this->customer?->name,
            'customer_avatar' => MediaPathNormalizer::url(
                $this->customer_avatar ?? $this->customer?->avatar
            ),
            'customer'        => $this->customer ? [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
                'avatar' => MediaPathNormalizer::url($this->customer->avatar),
            ] : null,
            'booking_id'      => $this->booking_id,
            'rating'          => $this->rating,
            'comment'         => $this->comment,
            'review_type'     => $this->review_type,
            'video_path'      => MediaPathNormalizer::url($this->video_path),
            'status'          => $this->status,
            'points_given'    => $this->points_given,
            'is_featured'     => (bool) $this->is_featured,
            'created_at'      => $this->created_at?->toIso8601String(),
            'updated_at'      => $this->updated_at?->toIso8601String(),
        ];
    }
}
