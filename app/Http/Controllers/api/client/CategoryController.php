<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\ServiceResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends BaseController
{
    /**
     * GET /api/client/categories
     * 
     * Returns a list of all active categories.
     */
    public function index(): JsonResponse
    {
        $categories = Category::where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get();

        return $this->success(CategoryResource::collection($categories), 'Categories retrieved successfully.');
    }

    public function show($slug): JsonResponse
    {
        $category = Category::where('slug', $slug)
            ->where('status', 'Active')
            ->with([
                'services' => function ($query) {
                    $query->where('status', 'Active');
                }
            ])
            ->first();

        if (!$category) {
            return $this->error('Category not found.', 404);
        }

        return $this->success(new CategoryResource($category), 'Category details retrieved successfully.');
    }
}
