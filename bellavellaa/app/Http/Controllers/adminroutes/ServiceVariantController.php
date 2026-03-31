<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Service;
use App\Models\ServiceVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceVariantController extends Controller
{
    /**
     * Store a newly created variant for a service.
     */
    public function store(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('service_variants', 'public');
        }

        $variant = $service->variants()->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'price' => $request->price,
            'duration_minutes' => $request->duration_minutes,
            'image' => $imagePath,
            'status' => 'Active',
            'sort_order' => $service->variants()->count() + 1,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Variant created successfully',
            'variant' => $variant
        ]);
    }

    /**
     * Update the specified variant.
     */
    public function update(Request $request, ServiceVariant $variant)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:0',
            'status' => 'sometimes|required|in:Active,Inactive',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $variant->image = $request->file('image')->store('service_variants', 'public');
        }

        $variant->update($request->only(['name', 'price', 'duration_minutes', 'status', 'sort_order']));
        
        if ($request->has('name')) {
            $variant->slug = Str::slug($request->name);
            $variant->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Variant updated successfully',
            'variant' => $variant
        ]);
    }

    /**
     * Remove the specified variant.
     */
    public function destroy(ServiceVariant $variant)
    {
        $variant->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Variant deleted successfully'
        ]);
    }
}
