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

            // 2. Process Transaction
            return DB::transaction(function () use ($professional, $amount, $amountInPaise, $request) {
                // Find cash wallet
                $wallet = Wallet::where('holder_type', 'professional')
                    ->where('holder_id', $professional->id)
                    ->where('type', 'cash')
                    ->lockForUpdate()
                    ->first();

                if (!$wallet) {
                    return $this->error('Wallet not found.', 404);
                }

                // --- Withdrawal Delay Enforcement (Hardened) ---
                $withdrawDelayDays = (int) (\App\Models\Setting::get('withdraw_delay_days') ?? 3);
                $cutoffDate = now()->subDays($withdrawDelayDays);

                $pendingEarningsPaise = \App\Models\Booking::where('professional_id', $professional->id)
                    ->where('status', 'completed')
                    ->whereNotNull('completed_at')
                    ->where('completed_at', '>', $cutoffDate)
                    ->get()
                    ->sum(fn($b) => ($b->price - ($b->commission ?? 0)) * 100);

                $availableBalancePaise = (float) max(0, $wallet->balance - $pendingEarningsPaise);
                $availableBalancePaise = min($availableBalancePaise, (float) $wallet->balance);

                if ($amountInPaise > $availableBalancePaise) {
                    $availableFormatted = number_format($availableBalancePaise / 100, 2);
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient available balance. You can only withdraw ₹{$availableFormatted} at this time. Remaining earnings are locked for {$withdrawDelayDays} days after job completion.",
                        'available_balance' => (float)($availableBalancePaise / 100)
                    ], 403);
                }
                // ------------------------------------

                // Create withdrawal record (PENDING)
                $withdrawal = WithdrawalRequest::create([
                    'professional_id' => $professional->id,
                    'amount' => $amountInPaise,
                    'method' => $request->method ?? 'direct',
                    'status' => WithdrawalRequest::STATUS_PENDING,
                    'transaction_reference' => 'PENDING-' . strtoupper(bin2hex(random_bytes(4))),
                ]);

                // Debit the wallet (Deduction on request - Option B)
                $wallet->debit(
                    $amountInPaise,
                    'withdrawal_request',
                    "Withdrawal request of ₹{$amount} (Pending)",
                    $withdrawal->id,
                    WithdrawalRequest::class
                );

                return $this->success($withdrawal, 'Withdrawal request submitted for approval.');
            });

        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Withdrawal Error: ' . $e->getMessage());
            return $this->error('An error occurred during withdrawal: ' . $e->getMessage(), 500);
        }
    }
}
