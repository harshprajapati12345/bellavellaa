<?php

namespace App\Http\Controllers\Api\Professionals;

use App\Models\KitProduct;
use App\Models\KitOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KitController extends BaseController
{
    /**
     * GET /api/professional/kit-products
     */
    public function products(Request $request): JsonResponse
    {
        $products = KitProduct::with('category')
            ->where('status', 'Active')
            ->get();

        return $this->success($products, 'Kit products retrieved.');
    }

    /**
     * POST /api/professional/payment/create-order
     * Create a Razorpay order before checkout
     */
    public function createPaymentOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kit_product_id' => 'required|exists:kit_products,id',
            'quantity'       => 'required|integer|min:1',
        ]);

        $product     = KitProduct::findOrFail($validated['kit_product_id']);
        $totalAmount = $product->price * $validated['quantity'];
        $amountPaise = (int) round($totalAmount * 100); // Razorpay uses paise

        // Create Razorpay Order server-side for security
        try {
            if (config('services.razorpay.mock')) {
                return $this->success([
                    'order_id'     => 'order_mock_' . strtolower(Str::random(14)),
                    'amount'       => $amountPaise,
                    'amount_inr'   => $totalAmount,
                    'currency'     => 'INR',
                    'product_name' => $product->name,
                    'receipt'      => 'kit_mock_' . Str::random(8),
                    'is_mock'      => true,
                ], 'Razorpay mock order created.');
            }

            $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            $order = $api->order->create([
                'receipt'  => 'kit_' . Str::random(8),
                'amount'   => $amountPaise,
                'currency' => 'INR',
                'notes'    => [
                    'product_id' => $product->id,
                    'professional_id' => $request->user('professional-api')->id
                ]
            ]);
            
            return $this->success([
                'order_id'     => $order['id'],
                'amount'       => $amountPaise,
                'amount_inr'   => $totalAmount,
                'currency'     => 'INR',
                'product_name' => $product->name,
                'receipt'      => $order['receipt'],
            ], 'Razorpay order created.');
        } catch (\Exception $e) {
            return $this->error('Failed to create Razorpay order: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/professional/payment/verify
     * Verify Razorpay payment + create kit order record
     */
    public function verifyPayment(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $validated = $request->validate([
            'kit_product_id'      => 'required|exists:kit_products,id',
            'quantity'            => 'required|integer|min:1',
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id'   => 'required|string',
            'razorpay_signature'  => 'required|string',
            'payment_method'      => 'nullable|string',
            'notes'               => 'nullable|string',
        ]);

        $product = KitProduct::findOrFail($validated['kit_product_id']);
        $totalAmount = $product->price * $validated['quantity'];

        if ($product->total_stock < $validated['quantity']) {
            return $this->error('Insufficient stock for this product.', 400);
        }

        // Secure Signature Verification
        try {
            if (!config('services.razorpay.mock')) {
                $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
                $attributes = [
                    'razorpay_order_id'   => $validated['razorpay_order_id'],
                    'razorpay_payment_id' => $validated['razorpay_payment_id'],
                    'razorpay_signature'  => $validated['razorpay_signature']
                ];
                $api->utility->verifyPaymentSignature($attributes);
            }
        } catch (\Exception $e) {
            return $this->error('Payment signature verification failed: ' . $e->getMessage(), 422);
        }

        $product->decrement('total_stock', $validated['quantity']);

        $order = KitOrder::create([
            'professional_id'   => $professional->id,
            'kit_product_id'    => $validated['kit_product_id'],
            'quantity'          => $validated['quantity'],
            'total_amount'      => $totalAmount,
            'payment_id'        => $validated['razorpay_payment_id'],
            'razorpay_order_id' => $validated['razorpay_order_id'],
            'payment_status'    => 'Paid',
            'payment_method'    => $validated['payment_method'] ?? 'Razorpay',
            'order_status'      => 'Processing',
            'status'            => 'Assigned',
            'notes'             => $validated['notes'] ?? null,
            'assigned_at'       => now(),
        ]);

        // Activate professional
        $professional->update(['kit_purchased' => true]);

        return $this->success($order->load('product'), 'Payment verified. Kit order placed and professional activated.');
    }

    /**
     * POST /api/professional/orders (Legacy — direct place without payment)
     */
    public function order(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $validated = $request->validate([
            'kit_product_id' => 'required|exists:kit_products,id',
            'quantity'       => 'required|integer|min:1',
            'notes'          => 'nullable|string',
        ]);

        $product = KitProduct::findOrFail($validated['kit_product_id']);
        $totalAmount = $product->price * $validated['quantity'];

        if ($product->total_stock < $validated['quantity']) {
            return $this->error('Insufficient stock for this product.', 400);
        }

        if ($professional->earnings_balance < $totalAmount) {
            return $this->error('Insufficient wallet balance.', 400);
        }

        $product->decrement('total_stock', $validated['quantity']);
        $professional->decrement('earnings_balance', $totalAmount);

        $order = KitOrder::create([
            'professional_id' => $professional->id,
            'kit_product_id'  => $validated['kit_product_id'],
            'quantity'        => $validated['quantity'],
            'total_amount'    => $totalAmount,
            'status'          => 'Assigned',
            'order_status'    => 'Processing',
            'payment_status'  => 'Paid',
            'payment_method'  => 'Wallet',
            'notes'           => $validated['notes'],
            'assigned_at'     => now(),
        ]);

        // Activate professional
        $professional->update(['kit_purchased' => true]);

        return $this->success($order, 'Kit order placed successfully and professional activated.');
    }

    /**
     * GET /api/professional/orders
     */
    public function orders(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $orders = KitOrder::where('professional_id', $professional->id)
            ->with('product.category')
            ->latest()
            ->get();

        return $this->success($orders, 'Kit orders retrieved.');
    }

    /**
     * GET /api/professional/orders/{id}
     */
    public function showOrder(Request $request, $id): JsonResponse
    {
        $professional = $request->user('professional-api');

        $order = KitOrder::where('professional_id', $professional->id)
            ->with('product.category')
            ->findOrFail($id);

        return $this->success($order, 'Kit order details retrieved.');
    }
}
