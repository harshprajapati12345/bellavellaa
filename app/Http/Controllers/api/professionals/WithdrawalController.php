<?php

namespace App\Http\Controllers\Api\Professionals;

use App\Models\Wallet;
use App\Models\WithdrawalRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WithdrawalController extends BaseController
{
    /**
     * GET /api/professional/withdrawals/history
     */
    public function history(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $withdrawals = WithdrawalRequest::where('professional_id', $professional->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $this->success($withdrawals, 'Withdrawal history retrieved.');
    }

    /**
     * POST /api/professional/withdrawals/request
     */
    public function store(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');
        \Illuminate\Support\Facades\Log::info("Withdrawal request hit", ['pro_id' => $professional->id, 'data' => $request->all()]);

        try {
            // 1. Validate amount and method
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'amount' => 'required|numeric|min:100',
                'method' => 'nullable|string|in:upi,bank,direct',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $amount = $request->amount;
            $amountInPaise = (int)($amount * 100);
            $requestId = $request->request_id; // Frontend UUID

            return DB::transaction(function () use ($professional, $amount, $amountInPaise, $request, $requestId) {
                // 🔒 STEP 1: Lock Professional FIRST
                $professional = \App\Models\Professional::where('id', $professional->id)
                    ->lockForUpdate()
                    ->first();

                // 🔑 STEP 2: Idempotency Check
                if ($requestId && \App\Models\WithdrawalRequest::where('request_id', $requestId)->exists()) {
                    return $this->error('Duplicate withdrawal request.', 422);
                }

                // STEP 3: Cooldown Check
                $withdrawDelayDays = (int) (\App\Models\Setting::get('withdraw_delay_days') ?? 7);
                if ($professional->last_withdrawal_at) {
                    $nextAllowed = $professional->last_withdrawal_at->copy()->addDays($withdrawDelayDays);
                    if (now()->lt($nextAllowed)) {
                        return response()->json([
                            'success' => false,
                            'message' => "Withdrawal not allowed yet. Please wait for the cooldown.",
                            'next_withdrawal_at' => $nextAllowed->toIso8601String(),
                        ], 403);
                    }
                }

                // STEP 4: Balance Maturity Check (Ground Truth)
                $wallet = $professional->wallet;
                if (!$wallet) return $this->error('Wallet not found.', 404);

                $maturedBalancePaise = $wallet->transactions()
                    ->where('source', 'earnings')
                    ->where('type', 'credit')
                    ->matured($withdrawDelayDays)
                    ->sum('amount');
                
                // Also check total cash balance (sanity check)
                $actualAvailablePaise = min((int)$wallet->balance, (int)$maturedBalancePaise);

                if ($amountInPaise > $actualAvailablePaise) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient matured balance. Only ₹" . ($actualAvailablePaise / 100) . " is available.",
                        'available_balance' => $actualAvailablePaise / 100
                    ], 403);
                }

                // STEP 5: Create withdrawal record
                $withdrawal = WithdrawalRequest::create([
                    'professional_id' => $professional->id,
                    'amount' => $amountInPaise,
                    'method' => $request->input('method', 'direct'),
                    'status' => WithdrawalRequest::STATUS_PENDING,
                    'request_id' => $requestId,
                    'transaction_reference' => 'WDR-' . strtoupper(bin2hex(random_bytes(4))),
                ]);

                // Debit the wallet
                $wallet->debit(
                    $amountInPaise,
                    'withdrawal_request',
                    "Withdrawal of ₹{$amount}",
                    $withdrawal->id,
                    WithdrawalRequest::class
                );

                // Update cooldown
                $professional->update(['last_withdrawal_at' => now()]);

                return $this->success($withdrawal, 'Withdrawal request submitted.');
            });

        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Withdrawal Error: ' . $e->getMessage());
            return $this->error('An error occurred during withdrawal: ' . $e->getMessage(), 500);
        }
    }
}
