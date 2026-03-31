<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\KitCategory;
use App\Models\KitProduct;
use Illuminate\Http\Request;

class KitProductController extends Controller
{
    public function index()
    {
        $products = KitProduct::with('category')->get();
        $totalItems = $products->count();
        $lowStockCount = $products->filter(fn($p) => $p->available_stock <= $p->min_stock && $p->available_stock > 0)->count();
        $outOfStockCount = $products->filter(fn($p) => $p->available_stock == 0)->count();
        $totalValue = $products->sum(fn($p) => $p->price * $p->available_stock);

        return view('professionals.kit-products.index', compact('products', 'totalItems', 'lowStockCount', 'outOfStockCount', 'totalValue'));
    }

    public function create()
    {
        $categories = KitCategory::all();
        return view('professionals.kit-products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|unique:kit_products',
            'name' => 'required',
            'category_id' => 'required|exists:kit_categories,id',
            'total_stock' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->all();
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('kits', 'public');
            $data['image'] = $path;
        }

        KitProduct::create($data);

        return redirect()->route('kit-products.index')->with('success', 'Product added successfully.');
    }

    public function edit(KitProduct $kitProduct)
    {
        $categories = KitCategory::all();
        return view('professionals.kit-products.edit', compact('kitProduct', 'categories'));
    }

    public function update(Request $request, KitProduct $kitProduct)
    {
        $validated = $request->validate([
            'sku' => 'required|unique:kit_products,sku,'.$kitProduct->id,
            'name' => 'required',
            'category_id' => 'required|exists:kit_categories,id',
            'total_stock' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($kitProduct->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($kitProduct->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($kitProduct->image);
            }
            $path = $request->file('image')->store('kits', 'public');
            $data['image'] = $path;
        }

        $kitProduct->update($data);

        return redirect()->route('kit-products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(KitProduct $kitProduct)
    {
        $kitProduct->delete();
        return redirect()->route('kit-products.index')->with('success', 'Product deleted.');
    }
}
