<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$migrations = [
    '2026_03_26_161342_add_shift_tracking_to_professionals_table',
    '2026_03_26_163831_add_session_id_to_professionals_table',
    '2026_03_26_170335_harden_kit_orders_and_inventory_v2',
    '2026_03_26_170642_create_professional_kits_table',
    '2026_03_26_170752_add_kit_order_id_to_professional_kit_units_table',
    '2026_03_26_171240_add_idempotency_hash_to_kit_orders_table',
    '2026_03_26_173159_add_shift_duration_to_professionals_table',
];

$batch = DB::table('migrations')->max('batch') + 1;

foreach ($migrations as $migration) {
    if (!DB::table('migrations')->where('migration', $migration)->exists()) {
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch'     => $batch,
        ]);
        echo "Marked $migration as migrated (batch $batch)\n";
    } else {
        echo "$migration already marked as migrated\n";
    }
}
