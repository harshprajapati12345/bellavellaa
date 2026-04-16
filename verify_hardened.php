<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Professional;
use Illuminate\Support\Facades\DB;

// 1. Verify Column is gone
$columns = DB::select("SHOW COLUMNS FROM professionals");
$hasIsSuspended = collect($columns)->contains('Field', 'is_suspended');
echo "Column 'is_suspended' exists: " . ($hasIsSuspended ? 'YES' : 'NO') . "\n";

// 2. Verify State Transition
$p = Professional::first();
echo "Initial Status: {$p->status}\n";

$p->status = 'suspended';
$p->save();
$p->refresh();
echo "After Suspend Status: {$p->status}\n";

$p->status = 'active';
$p->save();
$p->refresh();
echo "After Unsuspend Status: {$p->status}\n";

// 3. Verify ensureActive Guard
try {
    $p->status = 'suspended';
    $p->save();
    echo "Attempting ensureActive as suspended...\n";
    $p->ensureActive();
} catch (\Exception $e) {
    echo "Guard Caught Exception: " . $e->getMessage() . "\n";
}

$p->status = 'active';
$p->save();
echo "Attempting ensureActive as active...\n";
$p->ensureActive();
echo "Guard Passed!\n";

// 4. Verify API response derivation
$resource = new \App\Http\Resources\Api\ProfessionalResource($p);
$data = $resource->resolve();
echo "API Resource 'is_suspended' value: " . ($data['is_suspended'] ? 'true' : 'false') . "\n";
