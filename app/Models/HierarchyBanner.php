<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class HierarchyBanner extends Model
{
    public const PLACEMENT_PAGE_HEADER = 'page_header';
    public const PLACEMENT_PROMO_BANNER = 'promo_banner';
    public const PLACEMENT_POPUP_BANNER = 'popup_banner';

    protected $fillable = [
        'title',
        'subtitle',
        'placement_type',
        'media_type',
        'media_path',
        'thumbnail_path',
        'target_type',
        'target_id',
        'action_link',
        'button_text',
        'sort_order',
        'status',
    ];

    public function target(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'target_type', 'target_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'Active');
    }

    public function scopeForTarget(
        Builder $query,
        string $targetType,
        int $targetId
    ): Builder {
        return $query
            ->where('target_type', $targetType)
            ->where('target_id', $targetId);
    }
}
