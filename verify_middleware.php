<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Professional;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckSuspendedProfessional;

$p = Professional::first();
$p->status = 'suspended';
$p->save();

$request = Request::create('/api/professional/dashboard', 'GET');
$request->setUserResolver(fn() => $p);

$middleware = new CheckSuspendedProfessional();
$response = $middleware->handle($request, function() {
    return response()->json(['success' => true]);
});

echo "Suspended Professional Response: " . $response->getContent() . "\n";

$p->status = 'active';
$p->save();

$response = $middleware->handle($request, function() {
    return response()->json(['success' => true]);
});

echo "Active Professional Response: " . $response->getContent() . "\n";
