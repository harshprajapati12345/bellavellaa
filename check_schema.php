<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

$out = "";
foreach(['professionals', 'kit_orders'] as $table) {
    $out .= "Table: $table\n";
    $out .= implode(', ', Schema::getColumnListing($table)) . "\n\n";
}
file_put_contents('schema_dump.txt', $out);
echo "Dumped to schema_dump.txt\n";
