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
}
