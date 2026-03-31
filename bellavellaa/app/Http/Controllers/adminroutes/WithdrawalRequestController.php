<?php

namespace App\Http\Controllers\adminroutes;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status'); // Show all by default
        $search = $request->get('search');

        $query = WithdrawalRequest::with('professional');

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('professional', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('professionals.withdrawals.index', compact('requests', 'status', 'search'));
    }

    public function history(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = WithdrawalRequest::with('professional')
            ->whereIn('status', ['paid', 'rejected', 'completed']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('professional', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $history = $query->orderBy('updated_at', 'desc')->paginate(15);

        return view('professionals.withdrawals.history', compact('history', 'search', 'status'));
    }

    public function approve(Request $request, $id)
    {
        $req = WithdrawalRequest::findOrFail($id);

        if ($req->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        DB::transaction(function () use ($req, $request) {
            $req->update([
                'status' => WithdrawalRequest::STATUS_COMPLETED,
                'transaction_reference' => $request->get('transaction_reference') ?? $req->transaction_reference,
                'admin_note' => $request->get('admin_note') ?? $req->admin_note,
                'processed_at' => now(),
            ]);
        });

        return back()->with('success', 'Withdrawal approved.');
    }

    public function reject(Request $request, $id)
    {
        $req = WithdrawalRequest::findOrFail($id);

        if ($req->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        DB::transaction(function () use ($req, $request) {
            // Update request status
            $req->update([
                'status' => 'rejected',
                'rejection_reason' => $request->get('rejection_reason') ?? 'Rejected by Admin',
                'admin_note' => $request->get('rejection_reason') ?? 'Rejected by Admin',
                'processed_at' => now(),
            ]);

            // Refund to professional's wallet (Deduction happened on request - Option B)
            $wallet = Wallet::where('holder_type', 'professional')
                ->where('holder_id', $req->professional_id)
                ->where('type', 'cash')
                ->lockForUpdate()
                ->first();

            if ($wallet) {
                $wallet->credit(
                    $req->amount,
                    'withdrawal_refund',
                    "Refund for rejected withdrawal request #{$req->id}",
                    $req->id,
                    WithdrawalRequest::class
                );
            }
        });

        return back()->with('success', 'Withdrawal rejected and amount refunded.');
    }
}
