<?php
include 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$firebase = app(\App\Services\FirebaseService::class);

$professionalId = 6; // Active pro
$bookingId = 4;

echo "--- TEST 1: SETTING STATUS TO PENDING ---\n";
$firebase->pushJobToFirestore($professionalId, [
    'booking_id' => $bookingId,
    'service'    => 'Deep Cleaning Service',
    'location'   => 'Mumbai, MH',
    'price'      => '1500',
    'status'     => 'pending'
]);
echo "Professional $professionalId document set to 'pending'. Flutter app should show popup.\n";

sleep(5);

echo "\n--- TEST 2: RESETTING STATUS TO IDLE ---\n";
$firebase->pushJobToFirestore($professionalId, [
    'booking_id' => $bookingId,
    'status'     => 'idle'
]);
echo "Professional $professionalId document reset to 'idle'.\n";
