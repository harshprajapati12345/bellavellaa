<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Category;
use App\Models\ServiceGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceGroupController extends Controller
{
    public function index()
    {
        $groups = ServiceGroup::with('category')->orderBy('sort_order')->get();
        return view('service-groups.index', compact('groups'));
    }

    public function create()
    {
        // Only 'services' type categories can have service groups
        $categories = Category::where('type', 'services')
            ->where('status', 'Active')
            ->orderBy('sort_order')
            ->get();
        return view('service-groups.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name'        => 'required|string|max:255',
            'tag_label'   => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        // Verify category is services-type
        $category = Category::whereKey($request->category_id)
            ->where('type', 'services')
            ->firstOrFail();

        // Auto-generate globally unique slug from category slug + group name
        $slug = ServiceGroup::generateSlug($category, $request->name);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('service-groups', 'public');
        }

        ServiceGroup::create([
            'category_id' => $category->id,
            'name'        => $request->name,
            'slug'        => $slug,
            'tag_label'   => $request->tag_label,
            'description' => $request->description,
            'image'       => $imagePath,
            'status'      => $request->has('status') ? 'Active' : 'Inactive',
            'sort_order'  => $request->sort_order ?? 0,
        ]);

        return redirect()->route('service-groups.index')
            ->with('success', 'Service group created successfully!');
    }

    public function edit(ServiceGroup $serviceGroup)
    {
        $categories = Category::where('type', 'services')
            ->where('status', 'Active')
            ->orderBy('sort_order')
            ->get();
        return view('service-groups.edit', compact('serviceGroup', 'categories'));
    }

    public function update(Request $request, ServiceGroup $serviceGroup)
    {
        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name'        => 'required|string|max:255',
            'tag_label'   => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $category = Category::whereKey($request->category_id)
            ->where('type', 'services')
            ->firstOrFail();

        // Regenerate slug if name or category changed
        $needsNewSlug = $request->name !== $serviceGroup->name
            || (int) $request->category_id !== (int) $serviceGroup->category_id;

        $slug = $needsNewSlug
            ? ServiceGroup::generateSlug($category, $request->name)
            : $serviceGroup->slug;

        $imagePath = $serviceGroup->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('service-groups', 'public');
        }

        $serviceGroup->update([
            'category_id' => $category->id,
            'name'        => $request->name,
            'slug'        => $slug,
            'tag_label'   => $request->tag_label,
            'description' => $request->description,
            'image'       => $imagePath,
            'status'      => $request->has('status') ? 'Active' : 'Inactive',
            'sort_order'  => $request->sort_order ?? $serviceGroup->sort_order,
        ]);

        return redirect()->route('service-groups.index')
            ->with('success', 'Service group updated successfully!');
    }

    public function destroy(ServiceGroup $serviceGroup)
    {
        $serviceGroup->delete();
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('service-groups.index')
            ->with('success', 'Service group deleted.');
    }

    /**
     * AJAX helper: returns active service groups for a given category.
     * Called by the service create/edit form JS to populate group dropdown.
     *
     * GET /admin/categories/{category}/service-groups
     */
    public function byCategory(Category $category)
    {
        $groups = $category->serviceGroups()
            ->where('status', 'Active')
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug']);

        return response()->json($groups);
    }
}
