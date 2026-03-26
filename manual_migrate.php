<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    if (!Schema::hasColumn('professionals', 'shift_duration')) {
        Schema::table('professionals', function ($table) {
            $table->integer('shift_duration')->default(480)->after('is_online');
        });
        echo "MANUAL MIGRATION SUCCESS: added shift_duration column.\n";
    } else {
        echo "MANUAL MIGRATION SKIPPED: shift_duration column already exists.\n";
    }
} catch (\Exception $e) {
    echo "MANUAL MIGRATION FAILED: " . $e->getMessage() . "\n";
}
