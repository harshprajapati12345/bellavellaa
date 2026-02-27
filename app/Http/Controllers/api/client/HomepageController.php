<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\ServiceResource;
use App\Models\Category;
use App\Models\HomepageContent;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class HomepageController extends BaseController
{
    /**
     * GET /api/client/homepage
     * 
     * Returns banners and featured sections for the client app home screen.
     */
    public function index(): JsonResponse
    {
        // 1. Get Banners from homepage_contents
        // Note: Flutter expects list of {title, subtitle, image}
        $banners = HomepageContent::where('section', 'hero_banner')
            ->where('status', 'Active')
            ->first();

        $bannerData = [];
        if ($banners && isset($banners->content['items'])) {
            $bannerData = $banners->content['items'];
        }

        // 2. Get Featured Categories
        $categories = Category::where('status', 'Active')
            ->where('featured', true)
            ->limit(8)
            ->get(['id', 'name', 'slug', 'image', 'color']);

        // 3. Get Popular Services (e.g., Hair Care)
        $popularServices = Service::where('status', 'Active')
            ->where('featured', true)
            ->limit(10)
            ->get();

        // 4. Get Trending Services (Placeholder for now, can be based on bookings)
        $trendingServices = Service::where('status', 'Active')
            ->orderBy('bookings', 'desc')
            ->limit(10)
            ->get();

        return $this->success([
            'banners' => $bannerData,
            'categories' => CategoryResource::collection($categories),
            'popular_services' => ServiceResource::collection($popularServices),
            'trending_services' => ServiceResource::collection($trendingServices),
            'active_bookings_count' => 0,
        ], 'Homepage data retrieved successfully.');
    }
}
