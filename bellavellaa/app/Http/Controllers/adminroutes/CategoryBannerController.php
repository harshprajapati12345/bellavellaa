<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Category;
use App\Models\CategoryBanner;
use App\Http\Requests\Admin\StoreCategoryBannerRequest;
use App\Http\Requests\Admin\UpdateCategoryBannerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryBannerController extends Controller
{
    public function index(Request $request)
    {
        $query = CategoryBanner::with('category');

        // Filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('banner_type')) {
            $query->where('banner_type', $request->banner_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $banners = $query->orderBy('sort_order')->paginate(10);
        $categories = Category::where('status', 'Active')->orderBy('name')->get();

        return view('category_banners.index', compact('banners', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('status', 'Active')->orderBy('name')->get();
        return view('category_banners.create', compact('categories'));
    }

    public function store(StoreCategoryBannerRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('media/banners', 'public');
        }

        $data['sort_order'] = $data['sort_order'] ?? 0;

        CategoryBanner::create($data);

        return redirect()->route('category-banners.index')
            ->with('success', 'Banner created successfully!');
    }

    public function edit(CategoryBanner $categoryBanner)
    {
        $categories = Category::where('status', 'Active')->orderBy('name')->get();
        return view('category_banners.edit', compact('categoryBanner', 'categories'));
    }

    public function update(UpdateCategoryBannerRequest $request, CategoryBanner $categoryBanner)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image
            if ($categoryBanner->image) {
                Storage::disk('public')->delete($categoryBanner->image);
            }
            $data['image'] = $request->file('image')->store('media/banners', 'public');
        }

        $data['sort_order'] = $data['sort_order'] ?? 0;

        $categoryBanner->update($data);

        return redirect()->route('category-banners.index')
            ->with('success', 'Banner updated successfully!');
    }

    public function destroy(CategoryBanner $categoryBanner)
    {
        if ($categoryBanner->image) {
            Storage::disk('public')->delete($categoryBanner->image);
        }
        
        $categoryBanner->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('category-banners.index')
            ->with('success', 'Banner deleted successfully!');
    }

    public function toggleStatus(Request $request, CategoryBanner $categoryBanner)
    {
        $categoryBanner->update(['status' => $request->status]);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Status updated successfully!');
    }
}
