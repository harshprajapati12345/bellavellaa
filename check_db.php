<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$cols = Illuminate\Support\Facades\Schema::getColumnListing('professionals');
print_r($cols);
$type = Illuminate\Support\Facades\Schema::getColumnType('professionals', 'status');
echo "\nStatus type: $type\n";
