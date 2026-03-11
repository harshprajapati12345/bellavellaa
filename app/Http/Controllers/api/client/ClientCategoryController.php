<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Resources\Api\CategoryBannerResource;
use App\Http\Resources\Api\CategoryDetailResource;
use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\SectionResource;
use App\Http\Resources\Api\ServiceGroupResource;
use App\Http\Resources\Api\ServiceResource;
use App\Models\Category;
use App\Models\CategoryBanner;
use App\Models\Service;
use Illuminate\Routing\Controller;

class ClientCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('status', 'Active')
            ->withCount([
                'serviceGroups as active_service_groups_count' => fn ($query) => $query->where('status', 'Active'),
                'directServices as active_direct_services_count' => fn ($query) => $query->where('status', 'Active'),
            ])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories),
        ]);
    }

    public function show($identifier)
    {
        $category = $this->findCategory($identifier);

        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category),
        ]);
    }

    public function serviceGroups($identifier)
    {
        $category = $this->findCategory($identifier, [
            'serviceGroups' => fn ($query) => $query->where('status', 'Active')->orderBy('sort_order')->withCount(['serviceTypes', 'directServices as services_count']),
        ]);

        return response()->json([
            'success' => true,
            'data' => ServiceGroupResource::collection($category->serviceGroups),
        ]);
    }

    public function pageData($identifier)
    {
        $category = $this->findCategory($identifier, [
            'banners' => fn ($query) => $query->where('status', 'Active')->orderBy('sort_order'),
        ]);

        $mostBooked = $category->services()
            ->where('status', 'Active')
            ->with('serviceType.serviceGroup.category')
            ->orderByDesc('bookings')
            ->take(5)
            ->get();

        $serviceGroups = $category->serviceGroups()
            ->where('status', 'Active')
            ->withCount(['serviceTypes', 'directServices as services_count'])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'category' => new CategoryResource($category),
                'banners' => CategoryBannerResource::collection($category->banners),
                'most_booked' => ServiceResource::collection($mostBooked),
                'service_groups' => ServiceGroupResource::collection($serviceGroups),
            ],
        ]);
    }

    public function details($identifier)
    {
        $category = $this->findCategory($identifier, [
            'serviceGroups' => fn ($query) => $query->where('status', 'Active')->orderBy('sort_order'),
            'serviceGroups.serviceTypes' => fn ($query) => $query->where('status', 'Active')->orderBy('sort_order'),
            'serviceGroups.serviceTypes.services' => fn ($query) => $query->where('status', 'Active')->orderBy('sort_order'),
        ]);

        return response()->json([
            'success' => true,
            'data' => new CategoryDetailResource($category),
        ]);
    }

    public function screenData($identifier)
    {
        $category = $this->findCategory($identifier);

        $sliderBanners = CategoryBanner::where('status', 'Active')
            ->where('banner_type', 'slider')
            ->orderBy('sort_order')
            ->get();

        $inlineBanners = CategoryBanner::where('status', 'Active')
            ->where('banner_type', 'promo')
            ->orderBy('sort_order')
            ->get();

        $sections = [];

        $serviceTypes = Category::where('status', 'Active')
            ->withCount([
                'serviceGroups as active_service_groups_count' => fn ($query) => $query->where('status', 'Active'),
                'directServices as active_direct_services_count' => fn ($query) => $query->where('status', 'Active'),
            ])
            ->orderBy('sort_order')
            ->get();

        $sections[] = [
            'type' => 'grid',
            'key' => 'service_types',
            'title' => 'Service Types',
            'items' => CategoryResource::collection($serviceTypes),
        ];

        $sections[] = [
            'type' => 'instagram',
            'key' => 'instagram_card',
            'title' => 'See Our Real Work',
            'items' => [],
        ];

        if ($inlineBanners->isNotEmpty()) {
            $sections[] = [
                'type' => 'banner',
                'key' => 'promo_banner_1',
                'title' => $inlineBanners[0]->title,
                'items' => [new CategoryBannerResource($inlineBanners[0])],
            ];
        }

        $mostBooked = $category->services()
            ->where('status', 'Active')
            ->orderByDesc('bookings')
            ->take(8)
            ->get();

        if ($mostBooked->isNotEmpty()) {
            $sections[] = [
                'type' => 'carousel',
                'key' => 'most_booked',
                'title' => 'Most Booked Services',
                'items' => ServiceResource::collection($mostBooked),
            ];
        }

        if ($inlineBanners->count() > 1) {
            $sections[] = [
                'type' => 'banner',
                'key' => 'promo_banner_2',
                'title' => $inlineBanners[1]->title,
                'items' => [new CategoryBannerResource($inlineBanners[1])],
            ];
        }

        $this->addCategorySection($sections, 'salon-for-women', 'salon-for-women-luxe', 'Salon for Women', 'Pamper yourself at home');

        if ($inlineBanners->count() > 2) {
            $sections[] = [
                'type' => 'banner',
                'key' => 'promo_banner_3',
                'title' => $inlineBanners[2]->title,
                'items' => [new CategoryBannerResource($inlineBanners[2])],
            ];
        }

        $this->addCategorySection($sections, 'hair-studio-for-women', null, 'Hair Studio for Women', 'Trendiest styles');
        $this->addCategorySection($sections, 'spa-for-women', 'spa-ayurveda', 'Spa for Women', 'Stress and pain relief');

        return response()->json([
            'success' => true,
            'data' => [
                'category' => new CategoryResource($category),
                'slider_banners' => CategoryBannerResource::collection($sliderBanners),
                'sections' => SectionResource::collection($sections),
            ],
        ]);
    }

    private function addCategorySection(array &$sections, string $categorySlug, ?string $groupSlug, string $title, string $subtitle): void
    {
        $query = Service::whereHas('category', fn ($builder) => $builder->where('slug', $categorySlug))
            ->where('status', 'Active');

        if ($groupSlug) {
            $query->whereHas('serviceGroup', fn ($builder) => $builder->where('slug', $groupSlug));
        }

        $services = $query->get();

        if ($services->isNotEmpty()) {
            $sections[] = [
                'type' => 'horizontal_list',
                'key' => str_replace('-', '_', $categorySlug),
                'title' => $title,
                'subtitle' => $subtitle,
                'items' => ServiceResource::collection($services),
            ];
        }
    }

    private function findCategory($identifier, array $with = [])
    {
        $query = Category::where('status', 'Active')
            ->withCount([
                'serviceGroups as active_service_groups_count' => fn ($builder) => $builder->where('status', 'Active'),
                'directServices as active_direct_services_count' => fn ($builder) => $builder->where('status', 'Active'),
            ]);

        if ($with !== []) {
            $query->with($with);
        }

        return $query->where(function ($builder) use ($identifier) {
            $builder->where('slug', $identifier);
            if (is_numeric($identifier)) {
                $builder->orWhere('id', $identifier);
            }
        })->firstOrFail();
    }
}
