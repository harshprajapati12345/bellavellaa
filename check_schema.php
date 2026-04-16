<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$res = \Illuminate\Support\Facades\DB::select("DESCRIBE professional_kits");
foreach ($res as $row) {
    echo $row->Field . " (" . $row->Type . ")\n";
}