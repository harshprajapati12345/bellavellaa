<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\KitOrder;
use App\Models\KitProduct;
use App\Http\Resources\Api\KitOrderResource;
use App\Http\Requests\Api\Admin\StoreKitAssignmentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KitOrderController extends BaseController
{
    /**
     * Display a listing of kit assignments.
     */
    public function index(): JsonResponse
    {
        $orders = KitOrder::with(['professional', 'kitProduct'])
            ->orderBy('assigned_at', 'desc')
            ->get();

        return $this->success(KitOrderResource::collection($orders), 'Kit assignments retrieved.');
    }

    /**
     * Assign a kit to a professional.
     */
    public function store(StoreKitAssignmentRequest $request): JsonResponse
    {
        $product = KitProduct::findOrFail($request->kit_product_id);

        if ($product->available_stock < $request->quantity) {
            return $this->error('Insufficient stock available.', 422);
        }

        $order = KitOrder::create([
            'professional_id' => $request->professional_id,
            'kit_product_id'  => $request->kit_product_id,
            'quantity'        => $request->quantity,
            'status'          => 'Assigned',
            'assigned_at'     => now(),
        ]);

        // Stock deduction logic - usually handled by observers or here
        $product->decrement('available_stock', $request->quantity);

        return $this->success(new KitOrderResource($order), 'Kit assigned successfully.', 201);
    }

    /**
     * Update the status of a kit assignment.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate(['status' => 'required|in:Assigned,In Transit,Received,Returned']);
        
        $order = KitOrder::findOrFail($id);
        
        $updateData = ['status' => $request->status];
        if ($request->status === 'Received') {
            $updateData['received_at'] = now();
        }

        $order->update($updateData);

        return $this->success(new KitOrderResource($order), "Kit status updated to {$request->status}.");
    }
}
