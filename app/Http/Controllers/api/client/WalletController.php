<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Client\BaseController;
use Illuminate\Http\JsonResponse;
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
}