<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\KitProduct;
use App\Http\Resources\Api\KitProductResource;
use App\Http\Requests\Api\Admin\StoreKitProductRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KitProductController extends BaseController
{
    /**
     * Display a listing of kit products.
     */
    public function index(): JsonResponse
    {
        $products = KitProduct::all();
        
        $stats = [
            'total_items'       => $products->count(),
            'low_stock_count'   => $products->filter(fn($p) => $p->available_stock <= $p->min_stock && $p->available_stock > 0)->count(),
            'out_of_stock_count' => $products->filter(fn($p) => $p->available_stock == 0)->count(),
        ];

        return $this->success([
            'stats'    => $stats,
            'products' => KitProductResource::collection($products)
        ], 'Kit products retrieved.');
    }

    /**
     * Store a newly created kit product.
     */
    public function store(StoreKitProductRequest $request): JsonResponse
    {
        $product = KitProduct::create($request->validated());
        return $this->success(new KitProductResource($product), 'Kit product added.', 201);
    }

    /**
     * Display the specified kit product.
     */
    public function show(KitProduct $kit_product): JsonResponse
    {
        return $this->success(new KitProductResource($kit_product), 'Kit product details retrieved.');
    }

    /**
     * Update the specified kit product.
     */
    public function update(Request $request, KitProduct $kit_product): JsonResponse
    {
        // Simple update since we're using generic request or specific one
        $kit_product->update($request->all());
        return $this->success(new KitProductResource($kit_product), 'Kit product updated.');
    }

    /**
     * Remove the specified kit product.
     */
    public function destroy(KitProduct $kit_product): JsonResponse
    {
        $kit_product->delete();
        return $this->success(null, 'Kit product deleted.');
    }
}
