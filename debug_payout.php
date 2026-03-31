<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$p = App\Models\Professional::find(1);
echo "NAME: " . $p->name . "\n";
echo "STATUS: " . $p->payout_verification_status . "\n";
echo "PAYOUT: " . json_encode($p->payout, JSON_PRETTY_PRINT) . "\n";
echo "UPDATED_AT: " . $p->updated_at . "\n";

$reqs = App\Models\VerificationRequest::where('professional_id', 1)->get();
echo "REQUESTS COUNT: " . $reqs->count() . "\n";
foreach ($reqs as $r) {
    echo " - ID: {$r->id}, Type: {$r->type}, Status: {$r->status}\n";
}
