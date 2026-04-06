<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearStuckProfessionalJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'professionals:clear-stuck-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets active_request_id for professionals whose bookings are no longer active';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = 0;
        
        // Final Hardened Logic: 
        // 1. Identify professionals whose active_request_id points to a booking that is CLOSED
        // 2. Identify professionals whose active_request_id is ORPHANED (booking deleted)
        // 3. Identify professionals whose active_request_id is STALE (Timeout > 30 mins)
        \App\Models\Professional::whereNotNull('active_request_id')
            ->where(function ($query) {
                $query->whereHas('activeBooking', function ($q) {
                    $q->whereNotIn('status', ['assigned', 'on_the_way', 'arrived', 'in_progress', 'accepted']);
                })
                ->orWhereDoesntHave('activeBooking')
                ->orWhere(function ($q) {
                    $q->whereNotNull('last_assigned_at')
                      ->where('last_assigned_at', '<', now()->subMinutes(30));
                });
            })
            ->chunkById(100, function ($professionals) use (&$count) {
                foreach ($professionals as $pro) {
                    \Illuminate\Support\Facades\Log::info("Self-healing: Releasing stuck professional #{$pro->id}", [
                        'last_assigned_at' => $pro->last_assigned_at,
                        'active_request_id' => $pro->active_request_id,
                        'current_status' => $pro->status
                    ]);

                    $pro->update(['active_request_id' => null, 'last_assigned_at' => null]);
                    $pro->refresh();
                    broadcast(new \App\Events\ProfessionalStatusUpdated($pro));
                    $count++;
                }
            });

        $this->info("Cleared {$count} stuck professional jobs using hardened self-healing logic.");
    }
}
