<?php
include 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ps = \App\Models\Professional::all();
echo "ID\tNAME\tVERIF\tSTATUS\tONLINE\tAVAIL\tFCM_TOKEN\n";
foreach($ps as $p) {
    echo $p->id . "\t" . 
         str_pad(substr($p->name, 0, 15), 15) . "\t" . 
         str_pad($p->verification, 8) . "\t" . 
         str_pad($p->status, 8) . "\t" . 
         ($p->is_online ? 'YES' : 'NO ') . "\t" . 
         ($p->is_available ? 'YES' : 'NO ') . "\t" . 
         ($p->fcm_token ? substr($p->fcm_token, 0, 10) . '...' : 'MISSING') . "\n";
}
