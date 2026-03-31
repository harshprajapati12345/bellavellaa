<?php
include 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bookings = \App\Models\Booking::whereNull('professional_id')
    ->orWhereNotIn('status', ['completed', 'cancelled', 'rejected'])
    ->latest()
    ->limit(10)
    ->get();

echo "ID\tCUST\tPROF\tSTATUS\tPRICE\tSERVICE\n";
foreach($bookings as $b) {
    echo $b->id . "\t" . 
         ($b->customer_id ?? 'N/A') . "\t" . 
         ($b->professional_id ?? 'NULL') . "\t" . 
         str_pad($b->status, 12) . "\t" . 
         $b->price . "\t" . 
         ($b->service?->name ?? 'Service') . "\n";
}
