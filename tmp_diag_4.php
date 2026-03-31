<?php
include 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$b = \App\Models\Booking::find(4);
echo "--- BOOKING 4 ---\n";
echo "ID: " . $b->id . "\n";
echo "Customer ID: " . ($b->customer_id ?? 'NULL') . "\n";
echo "Professional ID: " . ($b->professional_id ?? 'NULL') . "\n";
echo "Status: " . $b->status . "\n";
echo "Price: " . $b->price . "\n";
echo "Date: " . $b->date . "\n";
echo "Slot: " . $b->slot . "\n";

$c = \App\Models\Customer::first();
if ($c) {
    echo "\nValid Customer ID available: " . $c->id . " (" . $c->name . ")\n";
} else {
    echo "\nNO CUSTOMERS FOUND IN DB!\n";
}
