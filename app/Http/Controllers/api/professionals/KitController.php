<?php

namespace App\Http\Controllers\Api\Professionals;

use App\Http\Resources\Api\KitOrderResource;
use App\Http\Resources\Api\KitProductResource;
use App\Models\KitOrder;
use App\Models\KitProduct;
use App\Models\ProfessionalKit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\WalletService;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class KitController extends BaseController
{
    /**
     * GET /api/professional/kits
     */
    public function kits(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $inventory = ProfessionalKit::with('product')
            ->where('professional_id', $professional->id)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->product ? $item->product->id : $item->product_id,
                    'name' => $item->product ? $item->product->name : 'Unknown Kit',
                    'qty' => $item->qty,
                    'image' => $item->product ? $item->product->image : null,
                ];
            });

        return $this->success($inventory, 'Professional kits inventory retrieved.');
    }

    /**
     * POST /api/professional/kits/repair - Self-healing inventory endpoint
     */
    public function repairInventory(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');
        
        $orders = KitOrder::where('professional_id', $professional->id)->get();
        // Group amounts and set total quantity directly to avoid infinite incrementing if run twice.
        $totals = [];
        foreach ($orders as $order) {
            $pid = $order->kit_product_id;
            if (!isset($totals[$pid])) {
                $totals[$pid] = 0;
            }
            $totals[$pid] += $order->quantity;
        }

        foreach ($totals as $pid => $totalQty) {
            ProfessionalKit::updateOrCreate(
                [
                    'professional_id' => $professional->id,
                    'product_id' => $pid
                ],
                [
                    'qty' => $totalQty
                ]
            );
        }

        return $this->success(null, 'Inventory successfully repaired from transaction log.');
    }

    /**
     * POST /api/professional/save-fcm-token
     */
    public function saveFcmToken(Request $request): JsonResponse
    {
        $request->validate(['token' => 'required|string']);
        $professional = $request->user('professional-api');
        
        $professional->update(['fcm_token' => $request->token]);
        
        return $this->success(null, 'FCM token saved successfully.');
    }

    /**
     * GET /api/professional/kit-products
     */
    public function products(Request $request): JsonResponse
    {
        $products = KitProduct::with('category')
            ->where('status', 'Active')
            ->get();

        return $this->success(KitProductResource::collection($products), 'Kit products retrieved.');
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
        $amountPaise = $product->price * $validated['quantity'];

        // Create Razorpay Order server-side for security
        try {
            if (config('services.razorpay.mock')) {
                return $this->success([
                    'order_id'     => 'order_mock_' . strtolower(Str::random(14)),
                    'amount'       => $amountPaise,
                    'amount_inr'   => (float)($amountPaise / 100),
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
                'amount_inr'   => (float)($amountPaise / 100),
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
     * Verify Razorpay payment + create kit order record (Hardenened Idempotency)
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

        // 1. Idempotency Check (Header OR Body)
        $idempotencyKey = $request->header('Idempotency-Key') ?? $request->input('idempotency_key');
        
        // Calculate hash excluding idempotency noise
        $hashData = $request->except(['idempotency_key', 'idempotency_hash']);
        $requestHash = hash('sha256', json_encode($hashData));

        if ($idempotencyKey) {
            $existing = KitOrder::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                if ($existing->idempotency_hash !== $requestHash && $request->has('idempotency_hash')) {
                     // If hash was provided and doesn't match, it's a reuse error
                     if ($existing->idempotency_hash !== $request->input('idempotency_hash')) {
                        return $this->error('Idempotency key reuse with different payload detected.', 400);
                     }
                }
                if ($existing->idempotency_response) {
                    return response()->json(json_decode($existing->idempotency_response, true));
                }
            }
        }

        // 2. Distributed Lock to prevent parallel race conditions
        $lockKey = 'kit_verify_' . $professional->id . '_' . $validated['razorpay_payment_id'];
        return Cache::lock($lockKey, 15)->block(5, function () use ($professional, $validated, $idempotencyKey, $requestHash) {
            
            $product = KitProduct::findOrFail($validated['kit_product_id']);
            $quantity = max(1, (int) $validated['quantity']);
            $amountPaise = $product->price * $quantity;

            // 3. Secure Signature Verification
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

            try {
                return DB::transaction(function () use ($professional, $product, $validated, $amountPaise, $quantity, $idempotencyKey, $requestHash) {
                    
                    // 4. Double check payment_id uniqueness inside transaction
                    if (KitOrder::where('payment_id', $validated['razorpay_payment_id'])->exists()) {
                         throw new \Exception('This payment has already been processed.');
                    }

                    // 5. Atomic Stock Check & Decrement
                    $product = KitProduct::where('id', $product->id)->lockForUpdate()->first();
                    if ($product->total_stock < $quantity) {
                        throw new \Exception('Insufficient stock for this product.');
                    }
                    $product->decrement('total_stock', $quantity);

                    // 6. Create Order with Idempotency Data
                    $order = KitOrder::create([
                        'idempotency_key'   => $idempotencyKey,
                        'idempotency_hash'  => $requestHash,
                        'professional_id'   => $professional->id,
                        'kit_product_id'    => $validated['kit_product_id'],
                        'quantity'          => $quantity,
                        'total_amount'      => $amountPaise,
                        'payment_id'        => $validated['razorpay_payment_id'],
                        'razorpay_order_id' => $validated['razorpay_order_id'],
                        'payment_status'    => 'Paid',
                        'payment_method'    => $validated['payment_method'] ?? 'Razorpay',
                        'order_status'      => 'Processing',
                        'status'            => 'Assigned',
                        'notes'             => $validated['notes'] ?? null,
                        'assigned_at'       => now(),
                    ]);

                    // 7. Update Professional Inventory
                    $professional->increment('kits', $quantity);
                    $professional->update(['kit_purchased' => true]);

                    $inventory = ProfessionalKit::firstOrCreate(
                        [
                            'professional_id' => $professional->id,
                            'product_id' => $product->id
                        ],
                        [
                            'qty' => 0
                        ]
                    );
                    $inventory->increment('qty', $quantity);

                    $responseData = [
                        'success' => true,
                        'message' => 'Payment verified. Kit order placed.',
                        'data'    => new KitOrderResource($order->load('product'))
                    ];
                    
                    // 8. Store Idempotency Response for Replay
                    $order->update(['idempotency_response' => json_encode($responseData)]);

                    // Notification Trigger
                    try {
                        if ($professional->fcm_token) {
                            $factory = (new Factory)->withServiceAccount(config('firebase.credentials') ?? config('services.firebase.credentials') ?? storage_path('firebase-credentials.json'));
                            $messaging = $factory->createMessaging();

                            $message = CloudMessage::withTarget('token', $professional->fcm_token)
                                ->withNotification(Notification::create(
                                    'Kit Purchased ✅',
                                    $product->name . ' added to your inventory'
                                ))
                                ->withData([
                                    'type' => 'kit_updated',
                                    'screen' => 'wallet',
                                    'product_name' => $product->name,
                                    'qty' => (string)$inventory->qty
                                ]);

                            $messaging->send($message);
                        }
                    } catch (\Exception $e) {
                        Log::error('FCM Notification Failed: ' . $e->getMessage());
                    }

                    return $this->success(new KitOrderResource($order->load('product')), 'Payment verified. Kit order placed.');
                });
            } catch (\Exception $e) {
                return $this->error($e->getMessage(), 400);
            }
        });
    }

    /**
     * POST /api/professional/orders (Wallet Purchase — Hardened)
     */
    public function order(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $validated = $request->validate([
            'kit_product_id' => 'required|exists:kit_products,id',
            'quantity'       => 'required|integer|min:1',
            'notes'          => 'nullable|string',
        ]);

        Log::info('📦 Kit Wallet Purchase: Starting', [
            'professional_id' => $professional->id,
            'request' => $request->all(),
            'idempotency_key' => $request->header('Idempotency-Key') ?? $request->input('idempotency_key')
        ]);

        // 1. Idempotency Check
        $idempotencyKey = $request->header('Idempotency-Key') ?? $request->input('idempotency_key');
        $hashData = $request->except(['idempotency_key', 'idempotency_hash']);
        $requestHash = hash('sha256', json_encode($hashData));

        if ($idempotencyKey) {
            $existing = KitOrder::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                if ($existing->idempotency_hash !== $requestHash && $request->has('idempotency_hash')) {
                     if ($existing->idempotency_hash !== $request->input('idempotency_hash')) {
                        return $this->error('Idempotency key reuse with different payload detected.', 400);
                     }
                }
                if ($existing->idempotency_response) {
                    return response()->json(json_decode($existing->idempotency_response, true));
                }
            }
        }

        // 2. Distributed Lock
        $lockKey = 'kit_wallet_purchase_' . $professional->id;
        return Cache::lock($lockKey, 10)->block(3, function () use ($professional, $validated, $idempotencyKey, $requestHash) {
            
            $product = KitProduct::findOrFail($validated['kit_product_id']);
            $quantity = max(1, (int) $validated['quantity']);

            Log::info('💰 MONEY TRACE [START]:', [
                'kit_product_id' => $product->id,
                'name'           => $product->name,
                'price_paise'    => $product->price,
                'quantity'       => $quantity,
                'total_paise'    => $product->price * $quantity
            ]);

            $amountPaise = $product->price * $quantity;

            try {
                return DB::transaction(function () use ($professional, $product, $validated, $amountPaise, $quantity, $idempotencyKey, $requestHash) {
                    
                    // 3. Wallet Lock & Check
                    $wallet = \App\Models\Wallet::where('holder_type', get_class($professional))
                        ->where('holder_id', $professional->id)
                        ->where('type', 'coin')
                        ->lockForUpdate()
                        ->first();

                    if (!$wallet) {
                        throw new \Exception('Cash wallet not found for this professional.');
                    }

                    Log::info('📦 Kit Wallet Purchase: Wallet Found', [
                        'wallet_id' => $wallet?->id,
                        'current_balance' => $wallet?->balance,
                        'required_paise' => $amountPaise,
                        'has_funds' => $wallet ? ($wallet->balance >= $amountPaise) : false
                    ]);

                    if (!$wallet || $wallet->balance < $amountPaise) {
                        throw new \Exception('Insufficient wallet balance.');
                    }

                    // 4. Product Lock & Check
                    $product = KitProduct::where('id', $product->id)->lockForUpdate()->first();
                    if ($product->total_stock < $quantity) {
                        throw new \Exception('Insufficient stock for this product.');
                    }

                    // 5. Atomic Execution
                    Log::info('📦 Kit Wallet Purchase: Deducting from wallet via WalletService...');
                    WalletService::deduct(
                        $wallet, 
                        $amountPaise, 
                        'kit_purchase', 
                        "Purchase of " . $product->name, 
                        null, 
                        'kit_order'
                    );
                    
                    Log::info('📦 Kit Wallet Purchase: Wallet deducted. Decrementing stock...');
                    $product->decrement('total_stock', $quantity);

                    $order = KitOrder::create([
                        'idempotency_key'   => $idempotencyKey,
                        'idempotency_hash'  => $requestHash,
                        'professional_id'   => $professional->id,
                        'kit_product_id'    => $product->id,
                        'quantity'          => $quantity,
                        'total_amount'      => $amountPaise, 
                        'status'            => 'Assigned',
                        'order_status'      => 'Processing',
                        'payment_status'    => 'Paid',
                        'payment_method'    => 'Wallet',
                        'notes'             => $validated['notes'] ?? null,
                        'assigned_at'       => now(),
                    ]);

                    // Update Professional Inventory
                    $professional->increment('kits', $quantity);
                    $professional->update(['kit_purchased' => true]);

                    $inventory = ProfessionalKit::firstOrCreate(
                        [
                            'professional_id' => $professional->id,
                            'product_id' => $product->id
                        ],
                        [
                            'qty' => 0
                        ]
                    );
                    $inventory->increment('qty', $quantity);

                    $responseData = [
                        'success' => true,
                        'message' => 'Kit purchased successfully via Wallet.',
                        'data'    => new KitOrderResource($order->load('product'))
                    ];
                    $order->update(['idempotency_response' => json_encode($responseData)]);

                    // Notification Trigger
                    try {
                        if ($professional->fcm_token) {
                            $factory = (new Factory)->withServiceAccount(config('firebase.credentials') ?? config('services.firebase.credentials') ?? storage_path('firebase-credentials.json'));
                            $messaging = $factory->createMessaging();

                            $message = CloudMessage::withTarget('token', $professional->fcm_token)
                                ->withNotification(Notification::create(
                                    'Kit Purchased ✅',
                                    $product->name . ' added to your inventory'
                                ))
                                ->withData([
                                    'type' => 'kit_updated',
                                    'screen' => 'wallet',
                                    'product_name' => $product->name,
                                    'qty' => (string)$inventory->qty
                                ]);

                            $messaging->send($message);
                        }
                    } catch (\Exception $e) {
                        Log::error('FCM Notification Failed: ' . $e->getMessage());
                    }

                    return response()->json($responseData);
                });
            } catch (\Exception $e) {
                return $this->error($e->getMessage(), 400);
            }
        });
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

        return $this->success(KitOrderResource::collection($orders), 'Kit orders retrieved.');
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

        return $this->success(new KitOrderResource($order), 'Kit order details retrieved.');
    }
}
