<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Api\Client\HomepageController;
use Illuminate\Http\Request;

echo "Verifying Homepage API...\n";

try {
    $controller = new HomepageController();
    $response = $controller->index();
    echo json_encode($response->getData(), JSON_PRETTY_PRINT);
    echo "\nAPI verification successful.\n";
} catch (\Exception $e) {
    echo "API verification failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
