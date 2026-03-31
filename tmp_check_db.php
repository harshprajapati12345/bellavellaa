<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
use Illuminate\Support\Facades\DB;
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$columns = DB::getSchemaBuilder()->getColumnListing('professionals');
echo "PROFESSIONALS COLUMNS: " . implode(', ', $columns) . "\n";

$verificationStatuses = DB::table('professionals')->distinct()->pluck('verification')->toArray();
echo "VERIFICATION STATUSES: " . implode(', ', $verificationStatuses) . "\n";
