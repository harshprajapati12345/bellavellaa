<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Client\BaseController;
use App\Http\Controllers\Api\Client\ProfileController;
use App\Http\Resources\Api\CartResource;
use App\Models\Cart;
use App\Models\Service;
use App\Models\Package;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Promotion;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends BaseController
{
    protected function guard()
    {
        return Auth::guard('api');
    }

    public function index(): JsonResponse
    {
        $cartItems = $this->guard()->user()->carts()->with('item')->get();

        $total = $cartItems->sum(function ($cart) {
            return $cart->quantity * ($cart->item->price ?? 0);
        });

        return $this->success([
            'items' => CartResource::collection($cartItems),
            'total' => (int) $total,
        ], 'Cart retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'item_type' => 'required|in:service,package',
            'item_id' => 'required|integer',
            'quantity' => 'integer|min:1',
        ]);

        $customerId = $this->guard()->id();

        // check if item exists
        if ($validated['item_type'] === 'service') {
            if (!Service::find($validated['item_id']))
                return $this->error('Service not found.', 404);
        } else {
            if (!Package::find($validated['item_id']))
                return $this->error('Package not found.', 404);
        }

        $cart = Cart::where([
            'customer_id' => $customerId,
            'item_type' => $validated['item_type'],
            'item_id' => $validated['item_id'],
        ])->first();

        if ($cart) {
            $cart->increment('quantity', $validated['quantity'] ?? 1);
        } else {
            $cart = Cart::create([
                'customer_id' => $customerId,
                'item_type' => $validated['item_type'],
                'item_id' => $validated['item_id'],
                'quantity' => $validated['quantity'] ?? 1,
            ]);
        }

        return $this->success(new CartResource($cart->refresh()), 'Item added to cart.');
    }

    public function update(Request $request, Cart $cart): JsonResponse
    {
        if ($cart->customer_id !== $this->guard()->id()) {
            return $this->error('Unauthorized.', 403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart->update($validated);

        return $this->success(new CartResource($cart), 'Cart updated.');
    }

    public function destroy(Cart $cart): JsonResponse
    {
        if ($cart->customer_id !== $this->guard()->id()) {
            return $this->error('Unauthorized.', 403);
        }

        $cart->delete();

        return $this->success(null, 'Item removed from cart.');
    }

    public function clear(): JsonResponse
    {
        $this->guard()->user()->carts()->delete();
        return $this->success(null, 'Cart cleared.');
    }

    public function checkout(Request $request): JsonResponse
    {
        $customer = $this->guard()->user();
        $cartItems = $customer->carts()->with('item')->get();

        if ($cartItems->isEmpty()) {
            return $this->error('Cart is empty.', 422);
        }

        $validated = $request->validate([
            'address' => 'required|string',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_slot' => 'required|string',
            'payment_method' => 'required|string', // online, cod, wallet
            'coupon_code' => 'nullable|string',
            'coins_used' => 'nullable|integer|min:0',
            'tip_amount_paise' => 'nullable|integer|min:0',
        ]);

        \DB::beginTransaction();
        try {
            $subtotalPaise = $cartItems->sum(function ($cart) {
                return $cart->quantity * ($cart->item->price ?? 0) * 100;
            });

            $discountPaise = 0;
            $promotionId = null;

            if (!empty($validated['coupon_code'])) {
                $promotion = Promotion::where('code', $validated['coupon_code'])->active()->first();
                if ($promotion && $subtotalPaise >= $promotion->min_order_paise) {
                    $promotionId = $promotion->id;
                    if ($promotion->type === 'percentage') {
                        $discountPaise = ($subtotalPaise * $promotion->value) / 100;
                        if ($promotion->max_discount_paise && $discountPaise > $promotion->max_discount_paise) {
                            $discountPaise = $promotion->max_discount_paise;
                        }
                    } elseif ($promotion->type === 'flat') {
                        $discountPaise = $promotion->value;
                    }
                }
            }

            $totalPaise = $subtotalPaise - $discountPaise + ($validated['tip_amount_paise'] ?? 0);
            if ($totalPaise < 0)
                $totalPaise = 0;

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'customer_id' => $customer->id,
                'address' => $validated['address'],
                'scheduled_date' => $validated['scheduled_date'],
                'scheduled_slot' => $validated['scheduled_slot'],
                'subtotal_paise' => $subtotalPaise,
                'discount_paise' => $discountPaise,
                'total_paise' => $totalPaise,
                'coins_used' => $validated['coins_used'] ?? 0,
                'payment_method' => $validated['payment_method'],
                'promotion_id' => $promotionId,
                'coupon_code' => $validated['coupon_code'] ?? null,
                'status' => 'pending',
                'customer_notes' => ($validated['tip_amount_paise'] ?? 0) > 0 ? "Tip included: ₹" . ($validated['tip_amount_paise'] / 100) : null,
            ]);

            foreach ($cartItems as $cart) {
                $order->items()->create([
                    'item_type' => $cart->item_type,
                    'item_id' => $cart->item_id,
                    'item_name' => $cart->item->name,
                    'quantity' => $cart->quantity,
                    'unit_price_paise' => ($cart->item->price ?? 0) * 100,
                    'total_price_paise' => ($cart->quantity * ($cart->item->price ?? 0)) * 100,
                ]);
            }

            // Clear cart
            $customer->carts()->delete();

            \DB::commit();

            return $this->success([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ], 'Order placed successfully.');

        } catch (\Exception $e) {
            \DB::rollBack();
            return $this->error('Failed to create order: ' . $e->getMessage(), 500);
        }
    }
}
