<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$res = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM professionals WHERE Field = 'status'");
print_r($res);
