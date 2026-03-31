<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Client\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends BaseController
{
    protected function guard()
    {
        return Auth::guard('api');
    }

    public function index(): JsonResponse
    {
        $customer = $this->guard()->user();
        
        // Explicitly select coin wallet only
        $coinWallet = $customer->coinWallet;

        if (!$coinWallet) {
            // Consistent null wallet response
            return $this->success([
                'wallet_type' => 'coin',
                'balance' => 0,
                'currency_label' => 'BellaVella Coins',
                'exchange_rate' => '1 Coin = ₹1.00',
                'transactions' => [],
            ], 'Wallet data retrieved successfully.');
        }

        $transactions = $coinWallet->transactions()
            ->latest()
            ->limit(10) // Limiting for improved performance as requested
            ->get(['id', 'type', 'amount', 'source', 'description', 'created_at'])
            ->map(function ($tx) {
                return [
                    'id' => (string) $tx->id,
                    'title' => $this->normalizeTransactionTitle($tx->description, $tx->source),
                    'date' => $tx->created_at->format('d M Y, h:i A'),
                    'amount' => (int) $tx->amount,
                    'type' => (string) $tx->type,
                ];
            })->values();

        // Get unscratched cards
        $scratchCards = \App\Models\ScratchCard::where('customer_id', $customer->id)
            ->where('is_scratched', false)
            ->latest()
            ->get();

        return $this->success([
            'wallet_type' => 'coin',
            'balance' => (int) $coinWallet->balance,
            'currency_label' => 'BellaVella Coins',
            'exchange_rate' => '1 Coin = ₹1.00',
            'transactions' => $transactions->toArray(),
            'scratch_cards' => $scratchCards,
        ], 'Wallet data retrieved successfully.');
    }

    /**
     * Normalize transaction title from description or source
     */
    private function normalizeTransactionTitle(?string $description, string $source): string
    {
        if ($description && trim($description) !== '') {
            return $description;
        }

        // Normalize source to human-readable title
        // daily_checkin -> Daily Check-in
        // referral_bonus -> Referral Bonus
        $normalized = str_replace('_', ' ', $source);
        $words = explode(' ', $normalized);
        $words = array_map(function ($word) {
            // Special handling for hyphenated words like "check-in"
            if (str_contains($word, '-')) {
                return implode('-', array_map('ucfirst', explode('-', $word)));
            }
            return ucfirst($word);
        }, $words);

        return implode(' ', $words);
    }

    public function deposit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $customer = $this->guard()->user();
        $wallet = $customer->coinWallet()->firstOrCreate([
            'holder_type' => 'customer',
            'type' => 'coin',
        ], [
            'balance' => 0,
            'version' => 1,
        ]);

        $wallet->credit($validated['amount'], 'deposit', $validated['description'] ?? 'Added coins to wallet');

        return $this->success(['balance' => $wallet->balance], 'Coins added to wallet successfully.');
    }

    public function withdraw(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $customer = $this->guard()->user();
        $wallet = $customer->coinWallet;

        if (!$wallet || $wallet->balance < $validated['amount']) {
            return $this->error('Insufficient balance.', 422);
        }

        try {
            $wallet->debit($validated['amount'], 'withdraw', $validated['description'] ?? 'Coins withdrawn from wallet');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }

        return $this->success(['balance' => $wallet->balance], 'Coins withdrawn successfully.');
    }
}