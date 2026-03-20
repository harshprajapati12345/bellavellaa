<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Resources\Api\CartResource;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Package;
use App\Models\Promotion;
use App\Models\Service;
use App\Models\ServiceVariant;
use App\Services\ConfigurablePackageService;
use App\Services\SellableServiceResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends BaseController
{
    public function __construct(
        protected SellableServiceResolver $sellableResolver,
        protected ConfigurablePackageService $packageService
    ) {
    }

    protected function guard()
    {
        return Auth::guard('api');
    }

    public function index(): JsonResponse
    {
        $cartItems = $this->guard()->user()->carts()->with(['item', 'service', 'variant.service', 'package'])->get();

        $total = $cartItems->sum(fn ($cart) => $cart->quantity * $cart->resolved_unit_price);

        return $this->success([
            'items' => CartResource::collection($cartItems),
            'total' => (float) $total,
        ], 'Cart retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'item_type' => 'required|in:service,variant,package',
            'item_id' => 'nullable|integer',
            'package_id' => 'nullable|integer|exists:packages,id',
            'service_id' => 'nullable|integer|exists:services,id',
            'service_variant_id' => 'nullable|integer|exists:service_variants,id',
            'context_type' => 'nullable|string',
            'context_id' => 'nullable|integer',
            'configuration' => 'nullable|array',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $customerId = $this->guard()->id();
        $quantity = $validated['quantity'] ?? 1;

        if ($validated['item_type'] === 'package') {
            $packageId = $validated['package_id'] ?? $validated['item_id'] ?? null;
            $package = Package::with(['contexts', 'groups.items.options'])->find($packageId);
            if (!$package) {
                return $this->error('Package not found.', 404);
            }

            $context = $this->packageService->assertPackageContext(
                $package,
                $validated['context_type'] ?? null,
                isset($validated['context_id']) ? (int) $validated['context_id'] : null,
            );
            $resolvedConfiguration = $this->packageService->buildResolvedConfiguration(
                $package,
                $validated['configuration'] ?? null,
            );
            $meta = $this->packageService->buildCartMeta(
                $package,
                $context,
                $resolvedConfiguration,
            );
            $configHash = data_get($meta, 'config_hash');
            $effectiveQuantity = $package->quantity_allowed ? $quantity : 1;

            $cart = Cart::query()
                ->where('customer_id', $customerId)
                ->where('item_type', 'package')
                ->where('package_id', $package->id)
                ->where('meta->config_hash', $configHash)
                ->first();

            if (!$cart) {
                $cart = Cart::create([
                    'customer_id' => $customerId,
                    'item_type' => 'package',
                    'item_id' => $package->id,
                    'package_id' => $package->id,
                    'quantity' => $effectiveQuantity,
                    'meta' => $meta,
                ]);
            } elseif ($package->quantity_allowed) {
                $cart->increment('quantity', $effectiveQuantity);
            } else {
                $cart->update([
                    'quantity' => 1,
                    'meta' => $meta,
                ]);
            }
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

        if ($validated['item_type'] !== 'package') {
            $cart->increment('quantity', $quantity);
        }

        return $this->success(new CartResource($cart->fresh(['item', 'service', 'variant.service', 'package'])), 'Item added to cart.');
    }

    public function update(Request $request, Cart $cart): JsonResponse
    {
        if ($cart->customer_id !== $this->guard()->id()) {
            return $this->error('Unauthorized.', 403);
        }

        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:1',
            'context_type' => 'nullable|string',
            'context_id' => 'nullable|integer',
            'configuration' => 'nullable|array',
        ]);

        if ($cart->item_type === 'package') {
            $package = Package::with(['contexts', 'groups.items.options'])
                ->findOrFail($cart->package_id ?? $cart->item_id);

            $context = $this->packageService->assertPackageContext(
                $package,
                $validated['context_type'] ?? data_get($cart->meta, 'context.type'),
                isset($validated['context_id'])
                    ? (int) $validated['context_id']
                    : data_get($cart->meta, 'context.id'),
            );
            $resolvedConfiguration = $this->packageService->buildResolvedConfiguration(
                $package,
                $validated['configuration'] ?? data_get($cart->meta, 'configuration'),
            );
            $meta = $this->packageService->buildCartMeta($package, $context, $resolvedConfiguration);

            $cart->update([
                'quantity' => $package->quantity_allowed
                    ? ($validated['quantity'] ?? $cart->quantity)
                    : 1,
                'meta' => $meta,
                'item_id' => $package->id,
                'package_id' => $package->id,
            ]);
        } else {
            $cart->update([
                'quantity' => $validated['quantity'] ?? $cart->quantity,
            ]);
        }

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

    public function sync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.item_type' => 'required|in:service,package',
            'items.*.item_id' => 'nullable|integer',
            'items.*.package_id' => 'nullable|integer|exists:packages,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.context_type' => 'nullable|string',
            'items.*.context_id' => 'nullable|integer',
            'items.*.configuration' => 'nullable|array',
        ]);

        $customer = $this->guard()->user();
        
        // Clear existing cart
        $customer->carts()->delete();

        // Add new items
        foreach ($validated['items'] as $item) {
            if ($item['item_type'] === 'package') {
                $package = Package::with(['contexts', 'groups.items.options'])
                    ->findOrFail($item['package_id'] ?? $item['item_id']);
                $context = $this->packageService->assertPackageContext(
                    $package,
                    $item['context_type'] ?? null,
                    isset($item['context_id']) ? (int) $item['context_id'] : null,
                );
                $resolvedConfiguration = $this->packageService->buildResolvedConfiguration(
                    $package,
                    $item['configuration'] ?? null,
                );

                Cart::create([
                    'customer_id' => $customer->id,
                    'item_type' => 'package',
                    'item_id' => $package->id,
                    'package_id' => $package->id,
                    'quantity' => $package->quantity_allowed ? $item['quantity'] : 1,
                    'meta' => $this->packageService->buildCartMeta(
                        $package,
                        $context,
                        $resolvedConfiguration,
                    ),
                ]);
                continue;
            }

            Cart::create([
                'customer_id' => $customer->id,
                'item_type' => $item['item_type'],
                'item_id' => $item['item_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        return $this->success(null, 'Cart synced successfully.');
    }

    public function getSlotsFromCart(): JsonResponse
    {
        $customer = $this->guard()->user();
        $cartItems = $customer->carts()->with(['service.category', 'variant.service.category', 'package'])->get();
        $allCategoryNames = $cartItems->map(function ($cart) {
            if ($cart->item_type === 'package') {
                return data_get($cart->meta, 'context.name')
                    ?? $cart->package?->category?->name
                    ?? null;
            }

            return $cart->variant?->service?->resolved_category?->name
                ?? $cart->service?->resolved_category?->name
                ?? null;
        })->filter()->unique()->values()->all();

        $slotsMap = [];
        $now = now();
        
        foreach ($allCategoryNames as $categoryName) {
            $isBridal = str_contains(strtolower($categoryName), 'brid');
            $dayRange = $isBridal ? 30 : 7;
            
            $availableDates = [];
            for ($i = 0; $i < $dayRange; $i++) {
                $date = $now->copy()->addDays($i);
                $availableDates[] = [
                    'date' => $date->format('Y-m-d'),
                    'formatted' => $i === 0 ? 'Today' : $date->format('D, d M'),
                    'is_available' => true
                ];
            }

            $slotsMap[$categoryName] = [
                'name' => $categoryName,
                'available_dates' => $availableDates
            ];
        }

        return $this->success([
            'slots' => $slotsMap,
        ], 'Slots fetched successfully based on cart.');
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
            'address_id' => 'nullable|integer|exists:addresses,id',
            'city' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
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
                $price = $cart->resolved_unit_price * 100;

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

            $selectedAddress = null;
            if (!empty($validated['address_id'])) {
                $selectedAddress = $customer->addresses()->whereKey($validated['address_id'])->first();
            }

            $resolvedCity = trim((string) ($selectedAddress?->city ?? $validated['city'] ?? ''));
            $resolvedPhone = trim((string) ($selectedAddress?->phone ?? $customer->mobile ?? ''));
            if ($resolvedCity === '') {
                Log::warning('Checkout city missing', [
                    'customer_id' => $customer->id,
                    'address_id' => $validated['address_id'] ?? null,
                    'raw_address' => $validated['address'],
                ]);

                return $this->error('Selected address is incomplete. City is missing.', 422);
            }

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'customer_id' => $customer->id,
                'address_id' => $selectedAddress?->id,
                'address' => $validated['address'],
                'city' => $resolvedCity,
                'latitude' => $validated['latitude'] ?? $selectedAddress?->latitude,
                'longitude' => $validated['longitude'] ?? $selectedAddress?->longitude,
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
                $displayPrice = (float) $cart->resolved_unit_price;
                $durationMinutes = (int) ($cart->resolved_duration_minutes ?? 0);
                $itemName = $cart->resolved_display_name;

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

                for ($i = 0; $i < $cart->quantity; $i++) {
                    \App\Models\Booking::create([
                        'order_id' => $order->id,
                        'customer_id' => $customer->id,
                        'customer_name' => $customer->name ?: ($resolvedPhone ?: 'Guest'),
                        'customer_phone' => $resolvedPhone,
                        'address_id' => $selectedAddress?->id,
                        'city' => $resolvedCity,
                        'lat' => $validated['latitude'] ?? $selectedAddress?->latitude,
                        'lng' => $validated['longitude'] ?? $selectedAddress?->longitude,
                        'service_id' => $cart->item_type === 'package' ? null : $cart->service_id,
                        'service_variant_id' => $cart->item_type === 'package' ? null : $cart->service_variant_id,
                        'service_name' => $cart->item_type === 'package' ? null : $cart->service?->name,
                        'package_id' => $cart->package_id,
                        'package_name' => data_get($cart->meta, 'package_snapshot.title') ?? $cart->package?->name,
                        'sellable_type' => $cart->item_type,
                        'sellable_id' => $cart->item_id,
                        'date' => $order->scheduled_date,
                        'slot' => $order->scheduled_slot,
                        'status' => 'pending',
                        'price' => $displayPrice,
                        'meta' => $cart->meta,
                    ]);
                }
            }

            // DO NOT clear cart here yet. It must wait for payment success.
            \DB::commit();

            $response = [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ];

            // If online payment, generate Razorpay order
            if ($validated['payment_method'] === 'online') {
                if (config('services.razorpay.mock')) {
                    $response['razorpay_order_id'] = 'order_mock_' . strtolower(Str::random(14));
                    $response['amount'] = $totalPaise;
                    $response['is_mock'] = true;
                } else {
                    try {
                        $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
                        $razorpayOrder = $api->order->create([
                            'receipt' => $order->order_number,
                            'amount' => $totalPaise,
                            'currency' => 'INR',
                        ]);
                        $response['razorpay_order_id'] = $razorpayOrder['id'];
                        $response['amount'] = $totalPaise;
                    } catch (\Exception $e) {
                        \DB::rollBack();
                        return $this->error('Failed to create Razorpay order: ' . $e->getMessage(), 500);
                    }
                }
            }

            // For non-online payments, clear the cart immediately
            if ($validated['payment_method'] !== 'online') {
                $customer->carts()->delete();
            }

            return $this->success($response, 'Order placed successfully.');

        } catch (\Exception $e) {
            \DB::rollBack();

            return $this->error('Failed to create order: ' . $e->getMessage(), 500);
        }
    }

    public function verifyCheckout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        try {
            if (!config('services.razorpay.mock')) {
                $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
                
                $attributes = array(
                    'razorpay_order_id' => $validated['razorpay_order_id'],
                    'razorpay_payment_id' => $validated['razorpay_payment_id'],
                    'razorpay_signature' => $validated['razorpay_signature']
                );
                
                $api->utility->verifyPaymentSignature($attributes);
            }

            $order = Order::findOrFail($validated['order_id']);
            
            // Mark order as confirmed and payment successful
            $order->update(['status' => 'confirmed']);

            // Clear the cart securely after payment success
            $order->customer->carts()->delete();

            return $this->success(null, 'Payment verified successfully.');

        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            return $this->error('Invalid payment signature', 400);
        } catch (\Exception $e) {
            return $this->error('Verification failed: ' . $e->getMessage(), 500);
        }
    }
}


