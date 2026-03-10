<?php

namespace App\Http\Controllers\api\client;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ClientCategoryController extends Controller
{
    /**
     * List all active categories.
     */
    public function index()
    {
        $categories = Category::where('status', 'Active')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Show a single category by ID.
     */
    public function show($id)
    {
        $category = Category::where('status', 'Active')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Fetch service groups belonging to this category.
     */
    public function serviceGroups($id)
    {
        $category = Category::where('status', 'Active')->findOrFail($id);
        
        $groups = $category->serviceGroups()
            ->where('status', 'Active')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $groups
        ]);
    }

    /**
     * COMPOSITE: Mobile landing page for a specific category.
     */
    public function pageData($id)
    {
        $category = Category::with([
            'banners' => fn($q) => $q->where('status', 'Active')->orderBy('sort_order')
        ])->where('status', 'Active')->findOrFail($id);

        $mostBooked = $category->services()
            ->where('status', 'Active')
            ->orderByDesc('review_count')
            ->take(5)
            ->get();
            
        $serviceGroups = $category->serviceGroups()
            ->where('status', 'Active')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'category' => $category,
                'banners' => $category->banners,
                'most_booked' => $mostBooked,
                'service_groups' => $serviceGroups
            ]
        ]);
    }

    /**
     * COMPOSITE: Deep nested services for detail screen.
     */
    public function details($id)
    {
        $category = Category::with([
            'serviceGroups' => fn($q) => $q->where('status', 'Active')->orderBy('sort_order'),
            'serviceGroups.services' => fn($q) => $q->where('status', 'Active')->orderBy('sort_order')
        ])->where('status', 'Active')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * COMPOSITE: Dynamic data for the Category Screen landing.
     */
    public function screenData($id)
    {
        $category = Category::where('status', 'Active')->findOrFail($id);

        // 1. Slider Banners
        $banners = $category->banners()
            ->where('status', 'Active')
            ->where('banner_type', 'slider')
            ->get();

        // 2. Promo Banners
        $promoBanners = $category->banners()
            ->where('status', 'Active')
            ->where('banner_type', 'promo')
            ->get();

        // 3. Service Types (All Categories)
        $serviceTypes = Category::where('status', 'Active')
            ->orderBy('sort_order')
            ->get();

        // 4. Most Booked Services
        $mostBooked = $category->services()
            ->where('status', 'Active')
            ->orderByDesc('bookings')
            ->take(8)
            ->get();

        // 5. Salon Categories (Luxe Services)
        $salonLuxe = \App\Models\Service::whereHas('category', fn($q) => $q->where('slug', 'salon-for-women'))
            ->whereHas('serviceGroup', fn($q) => $q->where('slug', 'salon-luxe'))
            ->where('status', 'Active')
            ->get();

        // 6. Spa Categories (Ayurveda Services)
        $spaAyurveda = \App\Models\Service::whereHas('category', fn($q) => $q->where('slug', 'spa-for-women'))
            ->whereHas('serviceGroup', fn($q) => $q->where('slug', 'spa-ayurveda'))
            ->where('status', 'Active')
            ->get();

        // 7. Hair Categories (Direct Hair Services)
        $hairServices = \App\Models\Service::whereHas('category', fn($q) => $q->where('slug', 'hair-studio-for-women'))
            ->where('status', 'Active')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'category' => $category,
                'banners' => $banners,
                'promo_banners' => $promoBanners,
                'service_types' => $serviceTypes,
                'most_booked' => $mostBooked,
                'salon_categories' => $salonLuxe,
                'spa_categories' => $spaAyurveda,
                'hair_categories' => $hairServices,
            ]
        ]);
    }
}
