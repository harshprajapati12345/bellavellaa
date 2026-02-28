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
        $coinWallet = $customer->coinWallet;

        if (!$coinWallet) {
            return $this->success([
                'balance' => 0,
                'transactions' => [],
            ], 'Wallet data retrieved successfully.');
        }

        $transactions = $coinWallet->transactions()
            ->latest()
            ->get(['id', 'type', 'amount', 'source', 'description', 'created_at'])
            ->map(function ($tx) {
                return [
                    'id' => $tx->id,
                    'title' => $tx->description ?? ucfirst(str_replace('_', ' ', $tx->source)),
                    'date' => $tx->created_at->format('d M Y, h:i A'),
                    'amount' => ($tx->type === 'credit' ? '+' : '-') . $tx->amount,
                    'type' => $tx->type,
                ];
            });

        return $this->success([
            'balance' => $coinWallet->balance,
            'transactions' => $transactions,
        ], 'Wallet data retrieved successfully.');
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