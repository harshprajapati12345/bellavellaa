<?php
include 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "SUCCESS: Database connection established.\n";
    
    $b = \App\Models\Booking::where('status', 'assigned')->orderBy('id', 'desc')->first();
    if ($b) {
        $p = \App\Models\Professional::find($b->professional_id);
        echo "LATEST_ASSIGNED_BOOKING:\n";
        echo "  - ID: " . $b->id . "\n";
        echo "  - Professional ID: " . $b->professional_id . "\n";
        echo "  - Status: " . $b->status . "\n";
        
        if ($p) {
            echo "ASSIGNED_PROFESSIONAL:\n";
            echo "  - Name: " . $p->name . "\n";
            echo "  - Verification: " . $p->verification . "\n";
            echo "  - Status: " . $p->status . "\n";
            echo "  - FCM Token: " . ($p->fcm_token ? 'SET' : 'MISSING') . "\n";
        } else {
            echo "  - Professional NOT FOUND in DB!\n";
        }
    } else {
        echo "DEBUG: No bookings found with status 'assigned'.\n";
    }

    $allAssigned = \App\Models\Booking::where('status', 'assigned')->count();
    echo "TOTAL_ASSIGNED: $allAssigned\n";

} catch (\Exception $e) {
    echo "FAILURE: Could not connect to the database.\n";
    echo "ERROR: " . $e->getMessage() . "\n";
}
