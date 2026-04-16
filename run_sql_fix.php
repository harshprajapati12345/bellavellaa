<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "1. Changing column to VARCHAR temporarily...\n";
    DB::statement("ALTER TABLE professionals MODIFY status VARCHAR(50) NOT NULL DEFAULT 'Active'");
    
    echo "2. Normalizing values to lowercase...\n";
    DB::statement("UPDATE professionals SET status = 'active' WHERE LOWER(status) = 'active'");
    DB::statement("UPDATE professionals SET status = 'suspended' WHERE LOWER(status) = 'suspended'");
    
    echo "3. Converting back to ENUM with lowercase only...\n";
    DB::statement("ALTER TABLE professionals MODIFY status ENUM('active', 'suspended') NOT NULL DEFAULT 'active'");
    
    echo "Success!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
