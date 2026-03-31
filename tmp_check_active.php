<?php
include 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$id = 6; // testing for hjnmk, (the active one)
$p = \App\Models\Professional::find($id);

echo "--- PROFESSIONAL INFO ---\n";
echo "ID: " . $p->id . "\n";
echo "Name: " . $p->name . "\n";
echo "Online: " . ($p->is_online ? 'YES' : 'NO') . "\n";

echo "\n--- ACTIVE JOB CHECK ---\n";
$booking = \App\Models\Booking::where('professional_id', $p->id)
    ->whereIn('status', ['assigned', 'accepted', 'on_the_way', 'arrived', 'in_progress', 'payment_pending'])
    ->latest()
    ->first();

if ($booking) {
    echo "Found Active Job ID: " . $booking->id . "\n";
    echo "Status: " . $booking->status . "\n";
    echo "Customer ID: " . $booking->customer_id . "\n";
} else {
    echo "No Active Job found for ID $id.\n";
}
