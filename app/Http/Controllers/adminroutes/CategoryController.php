<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['services', 'bookings'])->get();

        $totalCats = $categories->count();
        $totalSvcs = $categories->sum('services_count');
        $totalBookings = $categories->sum('bookings_count');
        $totalActive = $categories->where('status', 'Active')->count();
        $topCategory = $categories->sortByDesc('bookings_count')->first();

        return view('categories.index', compact(
            'categories',
            'totalCats',
            'totalSvcs',
            'totalBookings',
            'totalActive',
            'topCategory'
        ));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'type'       => ['required', Rule::in(['services', 'packages'])],
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        Category::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'type'        => $request->type,
            'sort_order'  => $request->sort_order ?? 0,
            'description' => $request->description,
            'status'      => $request->has('status') ? 'Active' : 'Inactive',
            'featured'    => $request->has('featured') ? 1 : 0,
            'color'       => $request->color ?? '#000000',
            'image'       => $imagePath,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function show(Category $category)
    {
        $category->loadCount('services');

        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
            'services_count' => $category->services_count,
            'status' => $category->status,
            'slug' => $category->slug,
            'image' => $category->image
                ? Storage::disk('public')->url($category->image)
                : null,
        ]);
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'type'       => ['required', Rule::in(['services', 'packages'])],
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $imagePath = $category->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $category->update([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'type'        => $request->type,
            'sort_order'  => $request->sort_order ?? $category->sort_order,
            'description' => $request->description,
            'status'      => $request->has('status') ? 'Active' : 'Inactive',
            'featured'    => $request->has('featured') ? 1 : 0,
            'color'       => $request->color ?? $category->color,
            'image'       => $imagePath,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('categories.index')
            ->with('success', 'Category deleted.');
    }

    public function toggleStatus(Request $request, Category $category)
    {
        $category->update(['status' => $request->status]);
        return response()->json(['success' => true]);
    }
}
