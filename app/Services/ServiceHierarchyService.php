<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceGroup;
use App\Models\ServiceType;
use App\Models\ServiceVariant;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ServiceHierarchyService
{
    public function __construct(
        protected HierarchyBannerService $bannerService
    ) {
    }

    public function resolveNode(string $nodeKey, ?string $level = null): array
    {
        $level = $this->normalizeLevel($level);

        return match ($level) {
            'category' => $this->categoryNode($this->findCategory($nodeKey)),
            'service_group' => $this->serviceGroupNode($this->findServiceGroup($nodeKey)),
            'service_type' => $this->serviceTypeNode($this->findServiceType($nodeKey)),
            'service' => $this->serviceNode($this->findService($nodeKey)),
            'variant' => $this->variantNode($this->findVariant($nodeKey)),
            default => $this->autoResolveNode($nodeKey),
        };
    }

    protected function autoResolveNode(string $nodeKey): array
    {
        foreach (['category', 'service_group', 'service_type', 'service', 'variant'] as $level) {
            try {
                return $this->resolveNode($nodeKey, $level);
            } catch (NotFoundHttpException $e) {
            }
        }

        throw new NotFoundHttpException('Hierarchy node not found.');
    }

    protected function categoryNode(Category $category): array
    {
        $category->load([
            'serviceGroups' => fn ($query) => $query->where('status', 'Active')->orderBy('sort_order'),
            'directServices' => fn ($query) => $query->where('status', 'Active')->orderBy('sort_order'),
        ]);

        $children = $category->serviceGroups->isNotEmpty() ? $category->serviceGroups : $category->directServices;
        $childrenType = $category->serviceGroups->isNotEmpty() ? 'service_groups' : 'services';

        return [
            'level' => 'category',
            'item' => $category,
            'children_type' => $childrenType,
            'children' => $children,
            'breadcrumbs' => [$category],
            'is_bookable' => false,
            'bookable_type' => null,
            'banners' => $this->bannerService->forTarget($category),
        ];
    }

    protected function serviceGroupNode(ServiceGroup $group): array
    {
        $group->load([
            'category',
            'serviceTypes' => fn ($query) => $query->where('status', 'Active')->orderBy('sort_order'),
            'directServices' => fn ($query) => $query->where('status', 'Active')->orderBy('sort_order'),
        ]);

        $children = $group->serviceTypes->isNotEmpty() ? $group->serviceTypes : $group->directServices;
        $childrenType = $group->serviceTypes->isNotEmpty() ? 'service_types' : 'services';

        return [
            'level' => 'service_group',
            'item' => $group,
            'children_type' => $childrenType,
            'children' => $children,
            'breadcrumbs' => [$group->category, $group],
            'is_bookable' => false,
            'bookable_type' => null,
            'banners' => $this->bannerService->forTarget($group),
        ];
    }

    protected function serviceTypeNode(ServiceType $type): array
    {
        $type->load([
            'serviceGroup.category',
            'services' => fn ($query) => $query->where('status', 'Active')->orderBy('sort_order'),
        ]);

        return [
            'level' => 'service_type',
            'item' => $type,
            'children_type' => 'services',
            'children' => $type->services,
            'breadcrumbs' => [$type->serviceGroup?->category, $type->serviceGroup, $type],
            'is_bookable' => false,
            'bookable_type' => null,
            'banners' => $this->bannerService->forTarget($type),
        ];
    }

    protected function serviceNode(Service $service): array
    {
        $service->load([
            'serviceType.serviceGroup.category',
            'variants' => fn ($query) => $query->where('status', 'Active')->orderBy('sort_order'),
            'includedItems' => fn ($query) => $query->orderBy('sort_order'),
            'addons' => fn ($query) => $query->where('status', 'Active')->orderBy('sort_order'),
        ]);

        $activeVariants = $service->variants;
        $hasVariants = $service->has_variants && $activeVariants->isNotEmpty();

        return [
            'level' => 'service',
            'item' => $service,
            'children_type' => $hasVariants ? 'variants' : null,
            'children' => $hasVariants ? $activeVariants : collect(),
            'breadcrumbs' => [
                $service->resolved_category,
                $service->resolved_service_group,
                $service->serviceType,
                $service,
            ],
            'is_bookable' => $service->canBeBookedDirectly(),
            'bookable_type' => $service->canBeBookedDirectly() ? 'service' : null,
            'has_variants' => $hasVariants,
            'banners' => $this->bannerService->forTarget($service),
        ];
    }

    protected function variantNode(ServiceVariant $variant): array
    {
        $variant->load('service.serviceType.serviceGroup.category');

        return [
            'level' => 'variant',
            'item' => $variant,
            'children_type' => null,
            'children' => collect(),
            'breadcrumbs' => [
                $variant->service?->resolved_category,
                $variant->service?->resolved_service_group,
                $variant->service?->serviceType,
                $variant->service,
                $variant,
            ],
            'is_bookable' => $variant->isBookable(),
            'bookable_type' => $variant->isBookable() ? 'variant' : null,
            'banners' => $this->bannerService->forTarget($variant),
        ];
    }

    protected function findCategory(string $nodeKey): Category
    {
        return Category::where('status', 'Active')
            ->where(function ($query) use ($nodeKey) {
                $query->where('slug', $nodeKey);
                if (is_numeric($nodeKey)) {
                    $query->orWhere('id', $nodeKey);
                }
            })
            ->firstOr(fn () => throw new NotFoundHttpException('Category not found.'));
    }

    protected function findServiceGroup(string $nodeKey): ServiceGroup
    {
        return ServiceGroup::where('status', 'Active')
            ->where(function ($query) use ($nodeKey) {
                $query->where('slug', $nodeKey);
                if (is_numeric($nodeKey)) {
                    $query->orWhere('id', $nodeKey);
                }
            })
            ->firstOr(fn () => throw new NotFoundHttpException('Service group not found.'));
    }

    protected function findServiceType(string $nodeKey): ServiceType
    {
        return ServiceType::where('status', 'Active')
            ->where(function ($query) use ($nodeKey) {
                $query->where('slug', $nodeKey);
                if (is_numeric($nodeKey)) {
                    $query->orWhere('id', $nodeKey);
                }
            })
            ->firstOr(fn () => throw new NotFoundHttpException('Service type not found.'));
    }

    protected function findService(string $nodeKey): Service
    {
        return Service::where('status', 'Active')
            ->where(function ($query) use ($nodeKey) {
                $query->where('slug', $nodeKey);
                if (is_numeric($nodeKey)) {
                    $query->orWhere('id', $nodeKey);
                }
            })
            ->firstOr(fn () => throw new NotFoundHttpException('Service not found.'));
    }

    protected function findVariant(string $nodeKey): ServiceVariant
    {
        return ServiceVariant::where('status', 'Active')
            ->where(function ($query) use ($nodeKey) {
                $query->where('slug', $nodeKey);
                if (is_numeric($nodeKey)) {
                    $query->orWhere('id', $nodeKey);
                }
            })
            ->firstOr(fn () => throw new NotFoundHttpException('Service variant not found.'));
    }

    protected function normalizeLevel(?string $level): ?string
    {
        if ($level === null || trim($level) === '') {
            return null;
        }

        return strtolower(str_replace([' ', '-'], '_', trim($level)));
    }
}
