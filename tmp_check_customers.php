<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$columns = Schema::getColumnListing('customers');
file_put_contents('customers_columns.txt', json_encode($columns));
echo "DONE\n";
