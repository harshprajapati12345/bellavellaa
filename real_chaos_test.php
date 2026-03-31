<?php

use App\Models\Order;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Professional;
use App\Models\Booking;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Mock Firebase
$firebaseMock = Mockery::mock(App\Services\FirebaseService::class)->shouldIgnoreMissing();
$app->instance(App\Services\FirebaseService::class, $firebaseMock);

$webhookSecret = 'chaos_secret_'.rand(100,999);
config([
    'services.razorpay.key' => 'rzp_test',
    'services.razorpay.secret' => 'rzp_secret',
    'services.razorpay.webhook_secret' => $webhookSecret
]);

function call($method, $uri, $params = [], $headers = [], $user = null, $guard = null) {
    if ($user && $guard) Auth::guard($guard)->setUser($user);
    $request = Request::create($uri, $method, $params, [], [], $headers, json_encode($params));
    $request->headers->set('Accept', 'application/json');
    if ($method !== 'GET') $request->headers->set('Content-Type', 'application/json');
    foreach($headers as $k => $v) $request->headers->set($k, $v);
    return app()->handle($request);
}

function generateSignature($payload, $secret) {
    return hash_hmac('sha256', $payload, $secret);
}

echo "--- HARDENED CHAOS AUDIT ---\n";

// ───── TEST 1: REAL SIGNATURE VALIDATION ─────
$payload1 = ['event' => 'payment.captured', 'payload' => ['payment' => ['entity' => ['id' => 'pay_1']]]];
$body1 = json_encode($payload1);
$sig1 = generateSignature($body1, $webhookSecret);

$res1 = call('POST', '/api/razorpay/webhook', $payload1, ['X-Razorpay-Signature' => $sig1]);
$test1Status = ($res1->getStatusCode() == 200) ? "PASS" : "FAIL";
echo "1. Real Signature Validation (Accepted): $test1Status (Code: {$res1->getStatusCode()})\n";

$res1_bad = call('POST', '/api/razorpay/webhook', $payload1, ['X-Razorpay-Signature' => 'wrong_sig']);
$test1_badStatus = ($res1_bad->getStatusCode() == 400) ? "PASS" : "FAIL";
echo "2. Real Signature Validation (Rejected): $test1_badStatus (Code: {$res1_bad->getStatusCode()})\n";


// ───── TEST 3: DOUBLE REFUND PROTECTION (IDEMPOTENCY) ─────
$wallet = Wallet::where('holder_id', 1)->where('holder_type', 'customer')->where('type', 'coin')->first();
DB::table('wallets')->where('id', $wallet->id)->update(['balance' => 5000]);
$wallet->refresh();
$initialBalance = $wallet->balance;

$order3 = Order::create([
    'order_number' => 'CH_DBL_'.uniqid(), 'customer_id' => 1,
    'total_paise' => 1000, 'final_payable_paise' => 700, 'coins_used' => 300,
    'payment_method' => 'ONLINE', 'payment_status' => 'PENDING', 'status' => 'pending',
    'scheduled_date' => now(), 'scheduled_slot' => '10:00 AM', 'address' => 'T', 'city' => 'T'
]);
$payment3 = Payment::create([
    'order_id' => $order3->id, 'customer_id' => 1, 'payment_method' => 'ONLINE',
    'gateway' => 'razorpay', 'gateway_order_id' => 'gate_dbl_'.uniqid(),
    'amount_paise' => 700, 'currency' => 'INR', 'status' => 'PENDING',
    'meta_json' => ['coins_used' => 300]
]);

$wallet->debit(300, 'checkout', 'Audit', $order3->id, 'Order');

$payload3 = ['event' => 'payment.failed', 'payload' => ['payment' => ['entity' => ['order_id' => $payment3->gateway_order_id, 'id' => 'fail_1', 'status' => 'failed']]]];
$body3 = json_encode($payload3);
$sig3 = generateSignature($body3, $webhookSecret);

// First Hit
call('POST', '/api/razorpay/webhook', $payload3, ['X-Razorpay-Signature' => $sig3]);
$balanceAfter1 = Wallet::where('id', $wallet->id)->value('balance');

// Second Hit (Double Webhook)
call('POST', '/api/razorpay/webhook', $payload3, ['X-Razorpay-Signature' => $sig3]);
$balanceAfter2 = Wallet::where('id', $wallet->id)->value('balance');

$test3Status = ($balanceAfter1 == $initialBalance && $balanceAfter2 == $initialBalance) ? "PASS" : "FAIL";
echo "3. Double Refund Protection: $test3Status (Initial: $initialBalance, After1: $balanceAfter1, After2: $balanceAfter2)\n";


// ───── TEST 4: PREPARE PARALLEL STRESS (STRESS.SH) ─────
$pro = Professional::first();
$order4 = Order::create([
    'order_number' => 'CH_STRESS_'.uniqid(), 'customer_id' => 1, 'total_paise' => 500, 'final_payable_paise' => 500,
    'payment_method' => 'cod', 'payment_status' => 'PENDING', 'status' => 'pending',
    'scheduled_date' => now(), 'scheduled_slot' => '10:00 AM', 'address' => 'T', 'city' => 'T'
]);
$booking = Booking::create([
    'order_id' => $order4->id, 'customer_id' => 1, 'professional_id' => $pro->id,
    'service_id' => 1, 'date' => now()->toDateString(), 'slot' => '10:00 AM', 'status' => 'payment_pending'
]);

// We can't easily run parallel curl from inside this script, but we can simulate the parallel logic 
// by checking if 'lockForUpdate' is actually being used in the controller code. 
// OR we can output a curl command for the user.

echo "4. Stress Test Prepared for Booking ID: {$booking->id}\n";
echo "   Run this in terminal to test real parallel concurrency:\n";
echo "   for i in {1..5}; do curl -X POST http://localhost:8000/api/professional/jobs/{$booking->id}/collect-cash & done\n";

echo "------------------------------\n";
