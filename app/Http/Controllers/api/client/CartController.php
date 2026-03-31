<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Resources\Api\CartResource;
use App\Models\Cart;
use App\Models\Offer;
use App\Models\OfferUsage;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Package;
use App\Models\Service;
use App\Models\ScratchCard;
use App\Events\ScratchCardCreated;



use App\Models\ServiceVariant;
use App\Models\Setting;
use App\Services\ConfigurablePackageService;
use App\Services\SellableServiceResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
            $dayRange = $isBridal ? 45 : 7;
            
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


    public function previewCheckout(Request $request): JsonResponse
    {
        $customer = $this->guard()->user();
        $cartItems = $customer->carts()->with(['item', 'service', 'variant.service', 'package'])->get();

        if ($cartItems->isEmpty()) {
            return $this->error('Cart is empty.', 422);
        }

        $validated = $request->validate([
            'payment_method' => 'required|string',
            'coupon_code' => 'nullable|string',
            'tip_amount_paise' => 'nullable|integer|min:0',
        ]);

        $subtotalPaise = $cartItems->sum(function ($cart) {
            $price = $cart->resolved_unit_price * 100;
            return $cart->quantity * (int) round($price);
        });

        $offerDiscountPaise = 0;
        $offerId = null;

        if (!empty($validated['coupon_code'])) {
            $offer = Offer::active()
                ->where('code', strtoupper($validated['coupon_code']))
                ->first();

            if ($offer && $subtotalPaise >= $offer->min_order_paise) {
                $usageCount = Schema::hasTable('offer_usages')
                    ? OfferUsage::query()
                        ->where('offer_id', $offer->id)
                        ->where('customer_id', $customer->id)
                        ->count()
                    : 0;

                if ($offer->per_user_limit === null || $usageCount < $offer->per_user_limit) {
                    $offerId = $offer->id;
                    $offerDiscountPaise = $offer->calculateDiscount((int) $subtotalPaise);
                }
            }
        }

        $tipAmountPaise = (int) ($validated['tip_amount_paise'] ?? 0);
        $payablePaise = max(0, $subtotalPaise - $offerDiscountPaise + $tipAmountPaise);

        return $this->success([
            'subtotal_paise' => $subtotalPaise,
            'payment_method' => $validated['payment_method'],
            'offer_id' => $offerId,
            'tip_amount_paise' => $tipAmountPaise,
            'offer_discount_paise' => $offerDiscountPaise,
            'total_discount_paise' => $offerDiscountPaise,
            'total_paise' => $payablePaise,
        ], 'Checkout preview calculated successfully.');
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
            'tip_amount_paise' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $subtotalPaise = $cartItems->sum(function ($cart) {
                $price = $cart->resolved_unit_price * 100;
                return $cart->quantity * (int) round($price);
            });

            $offerDiscountPaise = 0;
            $offerId = null;

            if (!empty($validated['coupon_code'])) {
                $offer = Offer::active()
                    ->where('code', strtoupper($validated['coupon_code']))
                    ->first();

                if ($offer && $subtotalPaise >= $offer->min_order_paise) {
                    $usageCount = Schema::hasTable('offer_usages')
                        ? OfferUsage::query()
                            ->where('offer_id', $offer->id)
                            ->where('customer_id', $customer->id)
                            ->count()
                        : 0;

                    if ($offer->per_user_limit === null || $usageCount < $offer->per_user_limit) {
                        $offerId = $offer->id;
                        $offerDiscountPaise = $offer->calculateDiscount((int) $subtotalPaise);
                    }
                }
            }

            $tipAmountPaise = (int) ($validated['tip_amount_paise'] ?? 0);
            $totalPaise = max(0, $subtotalPaise - $offerDiscountPaise + $tipAmountPaise);

            $selectedAddress = null;
            if (!empty($validated['address_id'])) {
                $selectedAddress = $customer->addresses()->whereKey($validated['address_id'])->first();
            }

            $resolvedCity = trim((string) ($selectedAddress?->city ?? $validated['city'] ?? ''));
            $resolvedPhone = trim((string) ($selectedAddress?->phone ?? $customer->mobile ?? ''));
            if ($resolvedCity === '') {
                return $this->error('Selected address is incomplete. City is missing.', 422);
            }

            $initialStatus = ($totalPaise === 0) ? 'confirmed' : 'pending';

            $orderPayload = [
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
                'discount_paise' => $offerDiscountPaise,
                'total_paise' => $totalPaise,
                'payment_method' => $validated['payment_method'],
                'coupon_code' => $validated['coupon_code'] ?? null,
                'status' => $initialStatus,
                'payment_status' => ($initialStatus === 'confirmed') ? 'captured' : 'pending',
                'customer_notes' => $tipAmountPaise > 0 ? 'Tip included: Rs ' . ($tipAmountPaise / 100) : null,
            ];

            if (Schema::hasColumn('orders', 'offer_id')) {
                $orderPayload['offer_id'] = $offerId;
            }

            $order = Order::create($orderPayload);

            // Create Payment Record (Step 3.2)
            $paymentMethodMapped = strtoupper($validated['payment_method']); // ONLINE, COD, WALLET
            $gatewayMapped = $validated['payment_method'] === 'online' ? 'razorpay' : ($validated['payment_method'] === 'wallet' ? 'wallet' : 'cash');
            
            $payment = Payment::create([
                'order_id' => $order->id,
                'customer_id' => $customer->id,
                'payment_method' => $paymentMethodMapped,
                'gateway' => $gatewayMapped,
                'amount_paise' => $finalPayablePaise,
                'status' => 'PENDING',
                'meta_json' => [
                    'wallet_redeemed_paise' => $walletRedeemedPaise,
                    'coins_used' => $actualCoinsUsed,
                    'snapshot' => $snapshot
                ]
            ]);

            // Handle immediate SUCCESS methods
            if ($validated['payment_method'] === 'wallet' || $finalPayablePaise === 0) {
                $payment->update(['status' => 'SUCCESS', 'paid_at' => now()]);
                $order->update(['payment_status' => 'SUCCESS', 'status' => 'confirmed']);
            }

            foreach ($cartItems as $cart) {
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

            if ($offerId !== null && $offerDiscountPaise > 0) {
                if (Schema::hasTable('offer_usages')) {
                    OfferUsage::create([
                        'offer_id' => $offerId,
                        'customer_id' => $customer->id,
                        'order_id' => $order->id,
                        'discount_paise' => $offerDiscountPaise,
                    ]);
                }

                if (Schema::hasColumn('offers', 'times_used')) {
                    Offer::whereKey($offerId)->increment('times_used');
                }
            }

            $response = [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'amount' => $totalPaise,
            ];

            if ($validated['payment_method'] === 'online' && $finalPayablePaise > 0) {
                if (config('services.razorpay.mock')) {
                    $mockOrderId = 'order_mock_' . strtolower(Str::random(14));
                    $payment->update(['gateway_order_id' => $mockOrderId]);
                    $response['razorpay_order_id'] = $mockOrderId;
                    $response['is_mock'] = true;
                } else {
                    $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
                    $razorpayOrder = $api->order->create([
                        'receipt' => $order->order_number,
                        'amount' => $totalPaise,
                        'currency' => 'INR',
                    ]);
                    $payment->update(['gateway_order_id' => $razorpayOrder['id']]);
                    $response['razorpay_order_id'] = $razorpayOrder['id'];
                }
            }

            if ($order->payment_status === 'SUCCESS') {
                $customer->carts()->delete();
            }

            DB::commit();
            return $this->success($response, 'Order placed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
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
                $attributes = [
                    'razorpay_order_id' => $validated['razorpay_order_id'],
                    'razorpay_payment_id' => $validated['razorpay_payment_id'],
                    'razorpay_signature' => $validated['razorpay_signature'],
                ];
                $api->utility->verifyPaymentSignature($attributes);
            }

            DB::transaction(function () use ($validated) {
                $order = Order::with('customer')->findOrFail($validated['order_id']);
                
                // Update Payment record
                $payment = Payment::where('order_id', $order->id)
                    ->where('gateway_order_id', $validated['razorpay_order_id'])
                    ->first();
                
                if ($payment) {
                    $payment->update([
                        'status' => 'SUCCESS',
                        'gateway_payment_id' => $validated['razorpay_payment_id'],
                        'gateway_signature' => $validated['razorpay_signature'],
                        'paid_at' => now(),
                    ]);
                }

                // Update Order
                $order->update([
                    'status' => 'confirmed',
                    'payment_status' => 'SUCCESS',
                ]);

                $order->customer->carts()->delete();

                // 🎯 Create Scratch Card Reward for Payment
                $user = $order->customer;
                
                // 🛡️ PRODUCTION HARDENING 🛡️
                // 1. Min value check (₹100 = 10000 paise)
                // 2. Idempotency via reference_id (1 per order)
                if ($order->total_paise < 10000) {
                    \Log::info("Order {$order->id} below ₹100. No scratch card issued.");
                    return;
                }

                $already = ScratchCard::where('customer_id', $user->id)
                    ->where('source', 'payment')
                    ->where('reference_id', $order->id)
                    ->exists();

                if (!$already) {
                    $rewards = [
                        ['amount' => 10, 'chance' => 50],
                        ['amount' => 20, 'chance' => 30],
                        ['amount' => 50, 'chance' => 15],
                        ['amount' => 100, 'chance' => 5],
                    ];

                    $rand = rand(1, 100);
                    $sum = 0;

                    foreach ($rewards as $reward) {
                        $sum += $reward['chance'];
                        if ($rand <= $sum) {
                            $card = ScratchCard::create([
                                'customer_id' => $user->id,

                                'amount' => $reward['amount'],
                                'title' => 'Payment Reward',
                                'description' => 'Great choice! Scratch & Win 🎊',
                                'source' => 'payment',
                                'reference_id' => $order->id,
                                'expires_at' => now()->addDays(30),
                            ]);
                            \Log::info("ScratchCard created for user {$user->id} for order {$order->id}");
                            
                            // 🔔 Dispatch Event for Push Notification (Async/Decoupled)
                            event(new ScratchCardCreated($user, $card));

                            break;


                        }
                    }
                }

            });


            return $this->success(null, 'Payment verified successfully.');
        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            // Update Payment to FAILED
            $payment = Payment::where('order_id', $request->order_id)
                ->where('gateway_order_id', $request->razorpay_order_id)
                ->first();
            
            if ($payment) {
                $payment->update(['status' => 'FAILED', 'meta_json->error' => 'Signature verification failed']);
                
                // Step 6.3: Auto refund wallet if it was a mixed payment
                $coinsUsed = data_get($payment->meta_json, 'coins_used', 0);
                if ($coinsUsed > 0) {
                    $order = $payment->order;
                    if ($order && $order->customer) {
                        $coinWallet = $order->customer->coinWallet;
                        if ($coinWallet) {
                            $coinWallet->credit($coinsUsed, 'refund', "Refund for failed payment of order #{$order->order_number}");
                        }
                    }
                }
            }
            
            return $this->error('Invalid payment signature', 400);
        } catch (\Exception $e) {
            return $this->error('Verification failed: ' . $e->getMessage(), 500);
        }
    }
}
