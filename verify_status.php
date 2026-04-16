<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Professional;

$p = Professional::first();
if (!$p) {
    echo "No professional found\n";
    exit;
}

echo "Initial - ID: {$p->id}, Status: {$p->status}, IsSuspended: " . ($p->is_suspended ? 'true' : 'false') . "\n";

$p->status = 'suspended';
$p->save();
$p = $p->fresh();
echo "After Suspend - Status: {$p->status}, IsSuspended: " . ($p->is_suspended ? 'true' : 'false') . "\n";

$p->status = 'active';
$p->save();
$p = $p->fresh();
echo "After Unsuspend - Status: {$p->status}, IsSuspended: " . ($p->is_suspended ? 'true' : 'false') . "\n";
