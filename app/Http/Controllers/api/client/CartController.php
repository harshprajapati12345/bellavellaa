<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Resources\Api\CartResource;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Package;
use App\Models\Promotion;
use App\Models\Service;
use App\Models\ServiceVariant;
use App\Services\SellableServiceResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends BaseController
{
    public function __construct(
        protected SellableServiceResolver $sellableResolver
    ) {
    }

    protected function guard()
    {
        return Auth::guard('api');
    }

    public function index(): JsonResponse
    {
        $cartItems = $this->guard()->user()->carts()->with(['item', 'service', 'variant.service', 'package'])->get();

        $total = $cartItems->sum(fn ($cart) => $cart->quantity * (($cart->sellable_item->display_price ?? $cart->sellable_item->price) ?? 0));

        return $this->success([
            'items' => CartResource::collection($cartItems),
            'total' => (float) $total,
        ], 'Cart retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'item_type' => 'required|in:service,variant,package',
            'item_id' => 'required|integer',
            'service_id' => 'nullable|integer|exists:services,id',
            'service_variant_id' => 'nullable|integer|exists:service_variants,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $customerId = $this->guard()->id();
        $quantity = $validated['quantity'] ?? 1;

        if ($validated['item_type'] === 'package') {
            $package = Package::find($validated['item_id']);
            if (!$package) {
                return $this->error('Package not found.', 404);
            }

            $cart = Cart::firstOrCreate([
                'customer_id' => $customerId,
                'item_type' => 'package',
                'item_id' => $package->id,
            ], [
                'package_id' => $package->id,
                'quantity' => 0,
            ]);
        } else {
            $service = Service::with('variants')->find($validated['service_id'] ?? ($validated['item_type'] === 'service' ? $validated['item_id'] : null));
            $variant = null;

            if ($validated['item_type'] === 'variant') {
                $variant = ServiceVariant::with('service')->find($validated['service_variant_id'] ?? $validated['item_id']);
                if (!$variant) {
                    return $this->error('Variant not found.', 404);
                }
                $service = $service ?? $variant->service;
            }

            if (!$service) {
                return $this->error('Service not found.', 404);
            }

            $resolved = $this->sellableResolver->resolveForService($service, $variant);
            $itemType = $resolved['bookable_type'] === 'variant' ? 'variant' : 'service';
            $itemId = $resolved['bookable_type'] === 'variant' ? $resolved['variant']->id : $service->id;

            $cart = Cart::firstOrCreate([
                'customer_id' => $customerId,
                'item_type' => $itemType,
                'item_id' => $itemId,
            ], [
                'service_id' => $service->id,
                'service_variant_id' => $resolved['variant']?->id,
                'quantity' => 0,
            ]);
        }

        $cart->increment('quantity', $quantity);

        return $this->success(new CartResource($cart->fresh(['item', 'service', 'variant.service', 'package'])), 'Item added to cart.');
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

        return $this->success(new CartResource($cart->fresh(['item', 'service', 'variant.service', 'package'])), 'Cart updated.');
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
        $cartItems = $customer->carts()->with(['item', 'service', 'variant.service', 'package'])->get();

        if ($cartItems->isEmpty()) {
            return $this->error('Cart is empty.', 422);
        }

        $validated = $request->validate([
            'address' => 'required|string',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_slot' => 'required|string',
            'payment_method' => 'required|string',
            'coupon_code' => 'nullable|string',
            'coins_used' => 'nullable|integer|min:0',
            'tip_amount_paise' => 'nullable|integer|min:0',
        ]);

        \DB::beginTransaction();

        try {
            $subtotalPaise = $cartItems->sum(function ($cart) {
                $price = ($cart->sellable_item->display_price ?? $cart->sellable_item->price ?? 0) * 100;

                return $cart->quantity * (int) round($price);
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

            $totalPaise = max(0, $subtotalPaise - $discountPaise + ($validated['tip_amount_paise'] ?? 0));

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'customer_id' => $customer->id,
                'address' => $validated['address'],
                'city' => $customer->city ?? 'Mumbai',
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
                'customer_notes' => ($validated['tip_amount_paise'] ?? 0) > 0 ? 'Tip included: Rs ' . (($validated['tip_amount_paise'] ?? 0) / 100) : null,
            ]);

            foreach ($cartItems as $cart) {
                $sellable = $cart->sellable_item;
                $displayPrice = (float) ($sellable->display_price ?? $sellable->price ?? 0);
                $durationMinutes = $sellable->resolved_duration_minutes ?? $sellable->duration_minutes ?? $sellable->duration ?? 0;
                $itemName = $sellable->name ?? 'Item';

                $order->items()->create([
                    'item_type' => $cart->item_type,
                    'item_id' => $cart->item_id,
                    'item_name' => $itemName,
                    'quantity' => $cart->quantity,
                    'unit_price_paise' => (int) round($displayPrice * 100),
                    'total_price_paise' => (int) round($cart->quantity * $displayPrice * 100),
                    'duration_minutes' => (int) $durationMinutes,
                    'service_id' => $cart->service_id,
                    'service_variant_id' => $cart->service_variant_id,
                    'package_id' => $cart->package_id,
                    'sellable_type' => $cart->item_type,
                    'sellable_id' => $cart->item_id,
                    'meta' => $cart->meta,
                ]);

                if ($cart->item_type === 'package') {
                    continue;
                }

                for ($i = 0; $i < $cart->quantity; $i++) {
                    \App\Models\Booking::create([
                        'order_id' => $order->id,
                        'customer_id' => $customer->id,
                        'customer_name' => $customer->name,
                        'customer_phone' => $customer->mobile,
                        'city' => $customer->city ?? 'Mumbai',
                        'service_id' => $cart->service_id,
                        'service_variant_id' => $cart->service_variant_id,
                        'service_name' => $cart->service?->name,
                        'package_id' => $cart->package_id,
                        'package_name' => $cart->package?->name,
                        'sellable_type' => $cart->item_type,
                        'sellable_id' => $cart->item_id,
                        'date' => $order->scheduled_date,
                        'slot' => $order->scheduled_slot,
                        'status' => 'Pending',
                        'price' => $displayPrice,
                    ]);
                }
            }

            $customer->carts()->delete();
            \DB::commit();

            return $this->success([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ], 'Order placed successfully.');
        } catch (\Throwable $exception) {
            \DB::rollBack();

            return $this->error('Failed to create order: ' . $exception->getMessage(), 500);
        }
    }
}
