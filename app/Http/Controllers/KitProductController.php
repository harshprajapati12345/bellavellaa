<?php

namespace App\Http\Controllers;

use App\Models\KitProduct;
use Illuminate\Http\Request;

class KitProductController extends Controller
{
    public function index()
    {
        $products = KitProduct::all();
        $totalItems = $products->count();
        $lowStockCount = $products->filter(fn($p) => $p->available_stock <= $p->min_stock && $p->available_stock > 0)->count();
        $outOfStockCount = $products->filter(fn($p) => $p->available_stock == 0)->count();
        $totalValue = $products->sum(fn($p) => $p->price * $p->available_stock);

        return view('professionals.kit-products.index', compact('products', 'totalItems', 'lowStockCount', 'outOfStockCount', 'totalValue'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|unique:kit_products',
            'name' => 'required',
            'total_stock' => 'required|integer|min:0',
        ]);

        KitProduct::create($request->all());

        return redirect()->route('kit-products.index')->with('success', 'Product added successfully.');
    }

    public function update(Request $request, KitProduct $kitProduct)
    {
        $validated = $request->validate([
            'sku' => 'required|unique:kit_products,sku,'.$kitProduct->id,
            'name' => 'required',
            'total_stock' => 'required|integer|min:0',
        ]);

        $kitProduct->update($request->all());

        return redirect()->route('kit-products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(KitProduct $kitProduct)
    {
        $kitProduct->delete();
        return redirect()->route('kit-products.index')->with('success', 'Product deleted.');
    }
}
