<?php

use App\Models\Booking;
use App\Models\Professional;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\BookingService;
use Illuminate\Support\Facades\DB;

// 1. Get a professional and a relevant booking
$pro = Professional::first();
if (!$pro) {
    die("No professional found. Please seed the database.\n");
}

// Ensure professional has a cash wallet
$wallet = Wallet::firstOrCreate(
    ['holder_id' => $pro->id, 'holder_type' => 'professional', 'type' => 'cash'],
    ['balance' => 0]
);

$initialBalance = $wallet->balance;
$initialEarnings = $pro->earnings;
$initialOrders = $pro->orders;

echo "Initial Stats for {$pro->name}:\n";
echo "Earnings: {$initialEarnings}\n";
echo "Orders: {$initialOrders}\n";
echo "Wallet Balance: " . ($initialBalance / 100) . " INR\n";

// 2. Create/Find a booking to complete
$booking = Booking::where('professional_id', $pro->id)
    ->where('status', '!=', 'Completed')
    ->first();

if (!$booking) {
    echo "Creating a test booking...\n";
    $booking = Booking::create([
        'professional_id' => $pro->id,
        'customer_id'     => \App\Models\Customer::first()->id ?? 1,
        'service_name'    => 'Test Service',
        'price'           => 1000,
        'commission'      => 100, // 10%
        'status'          => 'Accepted',
        'date'            => date('Y-m-d'),
        'slot'            => '10:00 AM',
    ]);
}

echo "\nCompleting Booking ID: {$booking->id} (Price: {$booking->price}, Commission: {$booking->commission})\n";

// 3. Execute Completion
try {
    BookingService::completeJob($booking);
} catch (\Exception $e) {
    die("Error during completion: " . $e->getMessage() . "\n");
}

// 4. Verification
$pro->refresh();
$wallet->refresh();
$transaction = WalletTransaction::where('wallet_id', $wallet->id)
    ->where('reference_id', $booking->id)
    ->latest()
    ->first();

echo "\nFinal Stats:\n";
echo "Earnings: {$pro->earnings} (+ " . ($pro->earnings - $initialEarnings) . ")\n";
echo "Orders: {$pro->orders} (+ " . ($pro->orders - $initialOrders) . ")\n";
echo "Wallet Balance: " . ($wallet->balance / 100) . " INR (+ " . (($wallet->balance - $initialBalance) / 100) . ")\n";

if ($transaction) {
    echo "\nTransaction Found:\n";
    echo "Type: {$transaction->type}\n";
    echo "Amount: " . ($transaction->amount / 100) . " INR\n";
    echo "Description: {$transaction->description}\n";
} else {
    echo "\nFAILED: Transaction not found!\n";
}

if ($booking->status === 'Completed') {
    echo "Booking Status: Completed [PASS]\n";
} else {
    echo "FAILED: Booking status is {$booking->status}\n";
}

if (($pro->earnings - $initialEarnings) == ($booking->price - $booking->commission)) {
    echo "Earnings Update: [PASS]\n";
} else {
    echo "FAILED: Earnings update mismatch.\n";
}
