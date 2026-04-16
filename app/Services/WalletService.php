<?php

namespace App\Services;

use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletService
{
    /**
     * Deduct funds from a professional's cash wallet with strict safety and ledger coordination.
     * 
     * @param Wallet $wallet The professional's cash wallet
     * @param int $amountPaise Amount to deduct in paise (smallest unit)
     * @param string $source The source category for the ledger (e.g., 'kit_purchase', 'withdrawal')
     * @param string $description Human-readable reason for the deduction
     * @param mixed $referenceId Optional reference ID (e.g., KitOrder ID)
     * @param string|null $referenceType Optional reference model type
     * @return bool
     * @throws \Exception
     */
    public static function deduct(Wallet $wallet, int $amountPaise, string $source, string $description, $referenceId = null, $referenceType = null): bool
    {
        // Model-level guard to prevent suspended professionals from performing outgoing transactions
        if ($wallet->holder instanceof \App\Models\Professional) {
            $wallet->holder->ensureActive();
        }

        return DB::transaction(function () use ($wallet, $amountPaise, $source, $description, $referenceId, $referenceType) {
            try {
                // Ensure sufficient balance at the query level before attempt (redundant but safe)
                if ($wallet->balance < $amountPaise) {
                    throw new \Exception("Insufficient balance in wallet for {$source}.");
                }

                $wallet->debit($amountPaise, $source, $description, $referenceId, $referenceType);
                
                Log::info("✅ WalletService: Successfully deducted {$amountPaise} paise", [
                    'wallet_id' => $wallet->id,
                    'source' => $source,
                    'reference_id' => $referenceId
                ]);

                return true;
            } catch (\Exception $e) {
                Log::error("❌ WalletService: Deduction FAILED", [
                    'wallet_id' => $wallet->id,
                    'source' => $source,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        });
    }
}
