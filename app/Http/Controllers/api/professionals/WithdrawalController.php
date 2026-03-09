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

        // 1. Check verification status
        if ($professional->payout_verification_status !== 'Verified') {
            return $this->error('Your account is not verified for withdrawals. Please complete your payout verification first.', 403);
        }

        // 2. Validate request
        $validated = $request->validate([
            'amount' => 'required|numeric|min:500|max:50000',
            'method' => ['required', Rule::in(['bank', 'upi'])],
            'bank_account_id' => 'required_if:method,bank|nullable|string',
            'upi_id' => 'required_if:method,upi|nullable|string',
        ]);

        $amountInPaise = (int) ($validated['amount'] * 100);

        // 3. Process Transaction
        return DB::transaction(function () use ($professional, $amountInPaise, $validated) {
            $wallet = Wallet::where('holder_type', 'professional')
                ->where('holder_id', $professional->id)
                ->where('type', 'cash')
                ->lockForUpdate()
                ->first();

            if (!$wallet || $wallet->balance < $amountInPaise) {
                return $this->error('Insufficient wallet balance.', 400);
            }

            // Debit the wallet immediately (Balance locking mechanism)
            $wallet->debit(
                $amountInPaise,
                'withdrawal_request',
                "Withdrawal request of ₹{$validated['amount']}",
                null,
                WithdrawalRequest::class
            );

            // Create withdrawal record
            $withdrawal = WithdrawalRequest::create([
                'professional_id' => $professional->id,
                'amount' => $amountInPaise,
                'method' => $validated['method'],
                'status' => WithdrawalRequest::STATUS_PENDING,
                'bank_account_id' => $validated['bank_account_id'] ?? null,
                'upi_id' => $validated['upi_id'] ?? null,
            ]);

            return $this->success($withdrawal, 'Withdrawal request submitted successfully.');
        });
    }
}
