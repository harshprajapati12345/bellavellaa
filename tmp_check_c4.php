<?php
include 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = \App\Models\Customer::find(4);
if ($c) {
    echo "Customer 4 Found: " . $c->name . "\n";
} else {
    echo "CUSTOMER 4 NOT FOUND IN DB!\n";
}
