<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Resources\Api\ServiceGroupResource;
use App\Http\Resources\Api\ServiceResource;
use App\Models\Category;
use App\Models\ServiceGroup;
use Illuminate\Http\JsonResponse;

class ServiceGroupController extends BaseController
{
    /**
     * GET /api/client/categories/{categorySlug}/groups/{groupSlug}/services
     *
     * Returns active services under a specific service group (e.g. Luxe, Prime).
     * Verifies that the group actually belongs to the given category in the URL
     * to prevent cross-category lookups like /salon-for-women/groups/spa-prime/services.
     */
    public function services(string $categorySlug, string $groupSlug): JsonResponse
    {
        // Verify category exists and is of services type
        $category = Category::where('slug', $categorySlug)
            ->where('status', 'Active')
            ->where('type', 'services')
            ->firstOrFail();

        // Verify group belongs to THIS category — prevents URL hierarchy bugs
        $group = ServiceGroup::where('slug', $groupSlug)
            ->where('category_id', $category->id)
            ->where('status', 'Active')
            ->firstOrFail();

        $services = $group->services()
            ->where('status', 'Active')
            ->orderBy('sort_order')
            ->get();

        return $this->success([
            'group'    => new ServiceGroupResource($group),
            'services' => ServiceResource::collection($services),
        ], 'Group services retrieved successfully.');
    }
}
