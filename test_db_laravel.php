<?php

define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "Laravel DB Connection: SUCCESS\n";
    echo "DB Name: " . \Illuminate\Support\Facades\DB::connection()->getDatabaseName() . "\n";
} catch (\Exception $e) {
    echo "Laravel DB Connection: FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
}
