<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WithdrawalRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    /**
     * GET /api/admin/withdrawals
     */
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');
        
        $query = WithdrawalRequest::with('professional')
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        $withdrawals = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $withdrawals
        ]);
    }

    /**
     * POST /api/admin/withdrawals/{id}/approve
     */
    public function approve(Request $request, $id): JsonResponse
    {
        $request->validate([
            'transaction_reference' => 'required|string',
            'admin_note' => 'nullable|string',
        ]);

        $withdrawal = WithdrawalRequest::findOrFail($id);

        if ($withdrawal->status !== WithdrawalRequest::STATUS_PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'This request is already ' . $withdrawal->status
            ], 422);
        }

        $withdrawal->update([
            'status' => WithdrawalRequest::STATUS_PAID,
            'transaction_reference' => $request->transaction_reference,
            'admin_note' => $request->admin_note,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal approved and marked as paid.',
            'data' => $withdrawal
        ]);
    }

    /**
     * POST /api/admin/withdrawals/{id}/reject
     */
    public function reject(Request $request, $id): JsonResponse
    {
        $request->validate([
            'admin_note' => 'required|string',
        ]);

        $withdrawal = WithdrawalRequest::findOrFail($id);

        if ($withdrawal->status !== WithdrawalRequest::STATUS_PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'This request is already ' . $withdrawal->status
            ], 422);
        }

        return DB::transaction(function () use ($withdrawal, $request) {
            // Update withdrawal status
            $withdrawal->update([
                'status' => WithdrawalRequest::STATUS_REJECTED,
                'admin_note' => $request->admin_note,
            ]);

            // Refund to professional's wallet
            $wallet = Wallet::where('holder_type', 'professional')
                ->where('holder_id', $withdrawal->professional_id)
                ->where('type', 'cash')
                ->lockForUpdate()
                ->first();

            if ($wallet) {
                $wallet->credit(
                    $withdrawal->amount,
                    'withdrawal_refund',
                    "Refund for rejected withdrawal request #{$withdrawal->id}",
                    $withdrawal->id,
                    WithdrawalRequest::class
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal rejected and amount refunded to wallet.',
                'data' => $withdrawal
            ]);
        });
    }
}
