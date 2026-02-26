<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['services', 'bookings'])->get();

        $totalCats     = $categories->count();
        $totalSvcs     = $categories->sum('services_count');
        $totalBookings = $categories->sum('bookings_count');
        $totalActive   = $categories->where('status', 'Active')->count();
        $topCategory   = $categories->sortByDesc('bookings_count')->first();

        return view('categories.index', compact(
            'categories', 'totalCats', 'totalSvcs', 'totalBookings', 'totalActive', 'topCategory'
        ));
    }

    public function create()
    {   
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
            $imagePath = asset('storage/' . $imagePath);
        }

        Category::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
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
            'image' => $category->image ? (str_starts_with($category->image, 'http') ? $category->image : asset('storage/'.$category->image)) : 'https://images.unsplash.com/photo-1596704017254-9b1b1b9e07f9?auto=format&fit=crop&w=400&q=80',
        ]);
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $imagePath = $category->image;
        if ($request->hasFile('image')) {
            $stored    = $request->file('image')->store('categories', 'public');
            $imagePath = asset('storage/' . $stored);
        }

        $category->update([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
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
        return redirect()->route('categories.index')
            ->with('success', 'Category deleted.');
    }

    public function toggleStatus(Request $request, Category $category)
    {
        $category->update(['status' => $request->status]);
        return response()->json(['success' => true]);
    }
}
