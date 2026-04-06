<?php

namespace App\Http\Controllers\Api\Professionals;

use App\Models\Wallet;
use App\Models\WithdrawalRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\WalletService;
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

            // 🔒 STRATEGY: Distributed Lock + DB Transaction
            $lockKey = 'withdrawal_pro_' . $professional->id;
            return Cache::lock($lockKey, 15)->block(10, function () use ($professional, $amount, $amountInPaise, $request, $requestId) {
                return DB::transaction(function () use ($professional, $amount, $amountInPaise, $request, $requestId) {
                    // 🔓 STEP 1: Row-level Lock
                    $professional = \App\Models\Professional::where('id', $professional->id)
                        ->lockForUpdate()
                        ->first();

                    // 🔑 STEP 2: Idempotency Check
                    if ($requestId && \App\Models\WithdrawalRequest::where('request_id', $requestId)->exists()) {
                        return $this->error('Duplicate withdrawal request.', 422);
                    }

                    // STEP 3: Cooldown Check
                    $cooldownDays = (int) (\App\Models\Setting::get('withdraw_cooldown_days', 7));
                    $lastWithdrawal = $professional->last_withdrawal_at;

                    if ($lastWithdrawal) {
                        $unlockDate = $lastWithdrawal->copy()->addDays($cooldownDays);
                        if (now()->lt($unlockDate)) {
                            return response()->json([
                                'success' => false,
                                'message' => "Withdraw allowed only once every $cooldownDays days",
                                'remaining_seconds' => max(0, $unlockDate->timestamp - now()->timestamp),
                                'lock_reason' => 'withdrawal_cooldown'
                            ], 403);
                        }
                    }

                    // STEP 4: Daily Limit
                    $withdrawalsToday = \App\Models\WithdrawalRequest::where('professional_id', $professional->id)
                        ->whereDate('created_at', \Carbon\Carbon::today())
                        ->count();
                    
                    if ($withdrawalsToday >= 3) {
                        return response()->json([
                            'success' => false,
                            'message' => "Daily withdrawal limit reached. Please try again tomorrow.",
                        ], 429);
                    }

                    // STEP 5: Balance Check (via cashWallet relationship)
                    $wallet = $professional->cashWallet()->lockForUpdate()->first();
                    if (!$wallet) return $this->error('Cash wallet not found.', 404);

                    $availablePaise = (int) $wallet->balance;
                    if ($amountInPaise > $availablePaise) {
                        return response()->json([
                            'success' => false,
                            'message' => "Insufficient balance.",
                        ], 403);
                    }

                    // STEP 6: Create withdrawal record
                    $withdrawal = WithdrawalRequest::create([
                        'professional_id' => $professional->id,
                        'amount' => $amountInPaise,
                        'method' => $request->input('method', 'direct'),
                        'status' => WithdrawalRequest::STATUS_PENDING,
                        'request_id' => $requestId,
                        'transaction_reference' => 'WDR-' . strtoupper(bin2hex(random_bytes(4))),
                    ]);

                    // 💸 STEP 7: Audit-safe Deduction
                    WalletService::deduct(
                        $wallet,
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
            });

        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Withdrawal Error: ' . $e->getMessage());
            return $this->error('An error occurred during withdrawal: ' . $e->getMessage(), 500);
        }
    }
}
