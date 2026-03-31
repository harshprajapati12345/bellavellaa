<?php

namespace App\Jobs;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ReconciliationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the daily reconciliation audit.
     */
    public function handle(): void
    {
        Log::info("ReconciliationJob: Starting daily wallet audit...");

        Wallet::chunk(100, function ($wallets) {
            foreach ($wallets as $wallet) {
                $transactionSum = WalletTransaction::where('wallet_id', $wallet->id)
                    ->selectRaw('SUM(CASE WHEN type IN ("credit", "razorpay", "recovery_rollback") THEN amount ELSE -amount END) as total')
                    ->value('total') ?? 0;

                // Simple check: current balance should equal sum of transactions
                $transactionSum = WalletTransaction::where('wallet_id', $wallet->id)
                    ->selectRaw('SUM(CASE WHEN type IN ("credit", "razorpay", "recovery_rollback") THEN amount ELSE -amount END) as total')
                    ->value('total') ?? 0;
                
                // Ensure we use absolute balance comparison if stored balance is in positive paise
                if (abs($wallet->balance - $transactionSum) > 100) { // Tolerance of 1 INR
                    Log::critical("WALLET_MISMATCH: Wallet ID {$wallet->id} (Holder: {$wallet->holder_type} #{$wallet->holder_id}) has balance drift!", [
                        'stored_balance' => $wallet->balance,
                        'transaction_sum' => $transactionSum,
                        'drift' => $wallet->balance - $transactionSum
                    ]);
                }
            }
        });

        Log::info("ReconciliationJob: Audit complete.");
    }
}
