<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

use App\Models\Customer;
use App\Helpers\RewardHelper;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$customer = Customer::first();
if ($customer) {
    RewardHelper::generateScratch($customer->id, 'welcome');
    echo "Generated welcome scratch card for customer: " . $customer->mobile . "\n";
} else {
    echo "No customer found.\n";
}
