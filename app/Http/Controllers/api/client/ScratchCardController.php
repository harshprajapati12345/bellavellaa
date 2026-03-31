<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Client\BaseController;
use App\Models\ScratchCard;
use App\Models\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScratchCardController extends BaseController
{
    /**
     * List all active scratch cards for the authenticated customer.
     */
    public function index(Request $request): JsonResponse
    {
        $cards = ScratchCard::where('customer_id', auth()->id())
            ->where('is_scratched', false)
            ->latest()
            ->get();

        return $this->success($cards, 'Scratch cards retrieved successfully.');
    }

    /**
     * Scratch a card and credit the user's wallet.
     * Uses database transaction and lockForUpdate to prevent race conditions.
     */
    public function scratch($id): JsonResponse
    {
        $user = auth()->user();

        try {
            DB::beginTransaction();

            // Lock for update to prevent double-scratching concurrently
            $card = ScratchCard::where('id', $id)
                ->where('customer_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($card->is_scratched) {
                DB::rollBack();
                return $this->error('This card has already been scratched.', 422);
            }

            // 1. Mark as scratched
            $card->update([
                'is_scratched' => true,
                'scratched_at' => now(),
            ]);

            // 2. Credit the wallet (using coins column as per WalletController index)
            $wallet = $user->coinWallet()->firstOrCreate([
                'holder_type' => 'customer',
                'type' => 'coin',
            ], [
                'balance' => 0,
                'version' => 1,
            ]);

            $wallet->increment('balance', $card->amount);

            // 3. Log transaction
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'credit',
                'amount' => $card->amount,
                'balance_after' => $wallet->balance,
                'source' => 'scratch',
                'reference_id' => $card->id,
                'reference_type' => 'scratch_card',
                'description' => "Reward from Scratch Card #{$card->id}",
            ]);

            DB::commit();

            return $this->success([
                'amount' => $card->amount,
                'new_balance' => $wallet->balance,
            ], 'Reward credited successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage() ?: 'Failed to process scratch card.', 500);
        }
    }
}
