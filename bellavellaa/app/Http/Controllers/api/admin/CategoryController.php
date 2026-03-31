<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use App\Http\Resources\Api\CategoryResource;
use App\Http\Requests\Api\Admin\StoreCategoryRequest;
use App\Http\Requests\Api\Admin\UpdateCategoryRequest;
use Illuminate\Http\JsonResponse;

class CategoryController extends BaseController
{
    /**
     * Display a listing of the categories.
     */
    public function index(): JsonResponse
    {
        $query = Category::query();

        if ($search = request('search')) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $categories = $query->latest()->paginate(request('per_page', 15));

        return $this->success([
            'categories' => CategoryResource::collection($categories),
            'pagination' => [
                'total' => $categories->total(),
                'count' => $categories->count(),
                'per_page' => $categories->perPage(),
                'current_page' => $categories->currentPage(),
                'total_pages' => $categories->lastPage(),
            ]
        ], 'Categories retrieved successfully.');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        return $this->success(new CategoryResource($category), 'Category created successfully.', 201);
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category): JsonResponse
    {
        return $this->success(new CategoryResource($category), 'Category retrieved successfully.');
    }

    /**
     * Update the specified category in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category->update($request->validated());

        return $this->success(new CategoryResource($category), 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return $this->success(null, 'Category deleted successfully.');
    }
}
