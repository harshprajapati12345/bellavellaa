<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
use Illuminate\Support\Facades\DB;
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo 'Categories: ' . DB::table('categories')->count() . "\n";
echo 'Bookings: ' . DB::table('bookings')->count() . "\n";
echo 'Customers: ' . DB::table('customers')->count() . "\n";
echo 'Professionals: ' . DB::table('professionals')->count() . "\n";
