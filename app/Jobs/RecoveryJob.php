<?php

namespace App\Jobs;

use App\Models\KitOrder;
use App\Models\Wallet;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecoveryJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job to recover or fail stalled orders.
     */
    public function handle(): void
    {
        // Find orders stuck in 'Pending' or 'Processing' for more than 30 minutes
        $stalledOrders = KitOrder::where('payment_status', '!=', 'Paid')
            ->where('created_at', '<', now()->subMinutes(30))
            ->where('order_status', 'Processing')
            ->get();

        foreach ($stalledOrders as $order) {
            DB::transaction(function () use ($order) {
                // Check if there's an associated wallet transaction that wasn't reverted
                $wallet = Wallet::where('holder_type', 'App\Models\Professional')
                    ->where('holder_id', $order->professional_id)
                    ->lockForUpdate()
                    ->first();

                if ($wallet) {
                    $transaction = $wallet->transactions()
                        ->where('reference_id', $order->id)
                        ->where('type', 'kit_purchase')
                        ->first();

                    if ($transaction) {
                        Log::warning("RecoveryJob: Rolling back stalled kit order #{$order->id} for Professional #{$order->professional_id}");
                        
                        // Revert the wallet debit
                        $wallet->credit(
                            $transaction->amount, 
                            'recovery_rollback', 
                            "Auto-rollback of failed order #{$order->id}", 
                            $order->id, 
                            'kit_order'
                        );
                        
                        // Revolve stock is optional but recommended if we track it strictly
                        $product = \App\Models\KitProduct::find($order->kit_product_id);
                        if ($product) {
                            $product->increment('total_stock', $order->quantity);
                        }
                    }
                }

                $order->update(['order_status' => 'Failed', 'payment_status' => 'Failed']);
            });
        }
    }
}
