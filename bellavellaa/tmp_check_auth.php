<?php
include 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = \App\Models\Professional::orderBy('last_seen', 'desc')->first();

if ($p) {
    echo "MOST RECENTLY ACTIVE PROFESSIONAL:\n";
    echo "ID: " . $p->id . "\n";
    echo "Name: " . $p->name . "\n";
    echo "Last Seen: " . $p->last_seen . "\n";
    echo "Is Online: " . ($p->is_online ? 'YES' : 'NO') . "\n";
} else {
    echo "NO PROFESSIONALS FOUND!\n";
}
