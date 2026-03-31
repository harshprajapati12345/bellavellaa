<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$count = App\Models\VerificationRequest::where('status', 'pending')->count();
echo "TOTAL PENDING REQUESTS: " . $count . "\n";

$all = App\Models\VerificationRequest::all();
foreach ($all as $r) {
    echo "ID: {$r->id}, Pro: {$r->professional_id}, Status: {$r->status}\n";
}
