<?php
include 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bookingId = 4;
$professionalId = 6; // Matching the recently active one

$booking = \App\Models\Booking::find($bookingId);
$booking->status = 'assigned';
$booking->professional_id = $professionalId;
$booking->save();

echo "Booking $bookingId Assigned to Professional $professionalId!\n";
