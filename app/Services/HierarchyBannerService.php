<?php

namespace App\Services;

use App\Models\Category;
use App\Models\HierarchyBanner;
use App\Models\Service;
use App\Models\ServiceGroup;
use App\Models\ServiceType;
use App\Models\ServiceVariant;
use Illuminate\Database\Eloquent\Model;

class HierarchyBannerService
{
    public function forTarget(?Model $target): array
    {
        if ($target === null) {
            return $this->emptyState();
        }

        $targetType = $this->resolveTargetType($target);
        if ($targetType === null) {
            return $this->emptyState();
        }

        $banners = HierarchyBanner::query()
            ->active()
            ->forTarget($targetType, (int) $target->getKey())
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('placement_type');

        return [
            HierarchyBanner::PLACEMENT_PAGE_HEADER => $banners->get(
                HierarchyBanner::PLACEMENT_PAGE_HEADER,
                collect()
            ),
            HierarchyBanner::PLACEMENT_PROMO_BANNER => $banners->get(
                HierarchyBanner::PLACEMENT_PROMO_BANNER,
                collect()
            ),
            HierarchyBanner::PLACEMENT_POPUP_BANNER => $banners->get(
                HierarchyBanner::PLACEMENT_POPUP_BANNER,
                collect()
            ),
        ];
    }

    protected function resolveTargetType(Model $target): ?string
    {
        return match (true) {
            $target instanceof Category => 'category',
            $target instanceof ServiceGroup => 'service_group',
            $target instanceof ServiceType => 'service_type',
            $target instanceof Service => 'service',
            $target instanceof ServiceVariant => 'variant',
            default => null,
        };
    }

    protected function emptyState(): array
    {
        return [
            HierarchyBanner::PLACEMENT_PAGE_HEADER => collect(),
            HierarchyBanner::PLACEMENT_PROMO_BANNER => collect(),
            HierarchyBanner::PLACEMENT_POPUP_BANNER => collect(),
        ];
    }
}
