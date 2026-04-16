<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$res = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM professionals WHERE Field = 'status'");
echo "ENUM Definition: " . $res[0]->Type . "\n";

$p = \App\Models\Professional::first();
echo "Model Result - ID: {$p->id}, Status: '{$p->status}', IsSuspended: " . ($p->is_suspended ? 'true' : 'false') . "\n";

$p->status = 'suspended';
$p->save();
$p = $p->fresh();
echo "After Suspend - Status: '{$p->status}', IsSuspended: " . ($p->is_suspended ? 'true' : 'false') . "\n";

$p->status = 'active';
$p->save();
$p = $p->fresh();
echo "After Unsuspend - Status: '{$p->status}', IsSuspended: " . ($p->is_suspended ? 'true' : 'false') . "\n";
