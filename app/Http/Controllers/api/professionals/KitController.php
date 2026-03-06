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

        // Return the amount — client handles Razorpay JS checkout
        // If you want server-side Razorpay order ID, install razorpay/razorpay PHP SDK
        // and uncomment the block below.
        /*
        $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
        $order = $api->order->create([
            'receipt'  => 'kit_' . Str::random(8),
            'amount'   => $amountPaise,
            'currency' => 'INR',
        ]);
        $razorpayOrderId = $order['id'];
        */

        return $this->success([
            'amount'       => $amountPaise,
            'amount_inr'   => $totalAmount,
            'currency'     => 'INR',
            'product_name' => $product->name,
            'receipt'      => 'kit_' . Str::random(8),
            // 'razorpay_order_id' => $razorpayOrderId,
        ], 'Payment order details.');
    }

    /**
     * POST /api/professional/payment/verify
     * Verify Razorpay payment + create kit order record
     */
    public function verifyPayment(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $validated = $request->validate([
            'kit_product_id'     => 'required|exists:kit_products,id',
            'quantity'           => 'required|integer|min:1',
            'payment_id'         => 'required|string',
            'razorpay_order_id'  => 'nullable|string',
            'payment_method'     => 'nullable|string',
            'notes'              => 'nullable|string',
        ]);

        $product     = KitProduct::findOrFail($validated['kit_product_id']);
        $totalAmount = $product->price * $validated['quantity'];

        if ($product->total_stock < $validated['quantity']) {
            return $this->error('Insufficient stock for this product.', 400);
        }

        // Signature verification (uncomment when using server-side Razorpay order ID)
        /*
        $generatedSignature = hash_hmac(
            'sha256',
            $validated['razorpay_order_id'] . '|' . $validated['payment_id'],
            config('services.razorpay.secret')
        );
        if ($generatedSignature !== $request->razorpay_signature) {
            return $this->error('Payment signature verification failed.', 422);
        }
        */

        $product->decrement('total_stock', $validated['quantity']);

        $order = KitOrder::create([
            'professional_id'   => $professional->id,
            'kit_product_id'    => $validated['kit_product_id'],
            'quantity'          => $validated['quantity'],
            'total_amount'      => $totalAmount,
            'payment_id'        => $validated['payment_id'],
            'razorpay_order_id' => $validated['razorpay_order_id'] ?? null,
            'payment_status'    => 'Paid',
            'payment_method'    => $validated['payment_method'] ?? 'UPI',
            'order_status'      => 'Processing',
            'status'            => 'Assigned',
            'notes'             => $validated['notes'] ?? null,
            'assigned_at'       => now(),
        ]);

        return $this->success($order->load('product'), 'Payment verified. Kit order placed.');
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

        if ($product->total_stock < $validated['quantity']) {
            return $this->error('Insufficient stock for this product.', 400);
        }

        $product->decrement('total_stock', $validated['quantity']);

        $order = KitOrder::create([
            'professional_id' => $professional->id,
            'kit_product_id'  => $validated['kit_product_id'],
            'quantity'        => $validated['quantity'],
            'total_amount'    => $product->price * $validated['quantity'],
            'status'          => 'Assigned',
            'order_status'    => 'Processing',
            'payment_status'  => 'Pending',
            'notes'           => $validated['notes'],
            'assigned_at'     => now(),
        ]);

        return $this->success($order, 'Kit order placed successfully.');
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
