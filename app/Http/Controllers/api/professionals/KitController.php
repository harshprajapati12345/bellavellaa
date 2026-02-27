<?php

namespace App\Http\Controllers\Api\Professionals;

use App\Models\KitProduct;
use App\Models\KitOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KitController extends BaseController
{
    /**
     * GET /api/professionals/kit-store
     * View available kit products and inventory
     */
    public function store(Request $request): JsonResponse
    {
        $products = KitProduct::where('status', 'Active')
            ->where('total_stock', '>', 0)
            ->get();

        return $this->success($products, 'Kit products retrieved.');
    }

    /**
     * POST /api/professionals/kit-orders
     * Place an order for kit items
     */
    public function order(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $validated = $request->validate([
            'kit_product_id' => 'required|exists:kit_products,id',
            'quantity'       => 'required|integer|min:1',
            'notes'          => 'nullable|string'
        ]);

        $product = KitProduct::findOrFail($validated['kit_product_id']);

        if ($product->total_stock < $validated['quantity']) {
            return $this->error('Insufficient stock for this product.', 400);
        }

        // Technically, a kit order assigns it out immediately or requires admin approval.
        // Assuming auto-assign if requested, so we deduct stock.
        $product->decrement('total_stock', $validated['quantity']);

        $order = KitOrder::create([
            'professional_id' => $professional->id,
            'kit_product_id'  => $validated['kit_product_id'],
            'quantity'        => $validated['quantity'],
            'status'          => 'Assigned', // Or 'Pending' if an enum exists
            'notes'           => $validated['notes'],
            'assigned_at'     => now(),
        ]);

        return $this->success($order, 'Kit order placed successfully.');
    }
}
