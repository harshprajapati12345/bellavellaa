<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\ServiceGroupResource;
use App\Http\Resources\Api\ServiceResource;
use App\Http\Resources\Api\PackageResource;
use App\Models\Category;
use App\Models\ServiceGroup;
use Illuminate\Http\JsonResponse;

class CategoryController extends BaseController
{
    /**
     * GET /api/client/categories
     *
     * Returns all active root categories ordered by sort_order.
     * Uses withCount to compute has_groups without N+1 queries.
     */
    public function index(): JsonResponse
    {
        $categories = Category::where('status', 'Active')
            ->withCount([
                'serviceGroups as active_service_groups_count' => fn($q) => $q->where('status', 'Active'),
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return $this->success(CategoryResource::collection($categories), 'Categories retrieved successfully.');
    }

    /**
     * GET /api/client/categories/{categorySlug}/groups
     *
     * Returns active service groups (Luxe, Prime, Ayurveda) under a services-type category.
     * Empty array if the category has no groups (e.g. Hair Studio).
     */
    public function groups(string $categorySlug): JsonResponse
    {
        $category = Category::where('slug', $categorySlug)
            ->where('status', 'Active')
            ->where('type', 'services')
            ->firstOrFail();

        $groups = $category->serviceGroups()
            ->where('status', 'Active')
            ->orderBy('sort_order')
            ->get();

        return $this->success(ServiceGroupResource::collection($groups), 'Service groups retrieved successfully.');
    }

    /**
     * GET /api/client/categories/{categorySlug}/services
     *
     * Returns services directly under a category with no group layer (e.g. Hair Studio).
     * Only fetches services where service_group_id IS NULL.
     */
    public function services(string $categorySlug): JsonResponse
    {
        $category = Category::where('slug', $categorySlug)
            ->where('status', 'Active')
            ->where('type', 'services')
            ->firstOrFail();

        $services = $category->directServices()
            ->where('status', 'Active')
            ->orderBy('sort_order')
            ->get();

        return $this->success([
            'category' => new CategoryResource($category),
            'services'  => ServiceResource::collection($services),
        ], 'Services retrieved successfully.');
    }

    /**
     * GET /api/client/categories/{categorySlug}/packages
     *
     * Returns active packages under a packages-type category (e.g. Bride).
     */
    public function packages(string $categorySlug): JsonResponse
    {
        $category = Category::where('slug', $categorySlug)
            ->where('type', 'packages')
            ->where('status', 'Active')
            ->firstOrFail();

        $packages = $category->packages()
            ->where('status', 'Active')
            ->with('services')   // pivot via services(), not servicesPivot()
            ->orderBy('sort_order')
            ->get();

        return $this->success([
            'category' => new CategoryResource($category),
            'packages'  => PackageResource::collection($packages),
        ], 'Packages retrieved successfully.');
    }
}
