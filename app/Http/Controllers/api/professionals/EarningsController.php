<?php

namespace App\Http\Controllers\Api\Professionals;

use App\Models\Booking;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EarningsController extends BaseController
{
    /**
     * GET /api/professionals/earnings
     * Overview of past and current earnings
     */
    public function index(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');
        
        $today = Carbon::today()->toDateString();
        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();

        // Calculate earnings from completed bookings
        // (Assuming earnings = price - commission% are stored or we calculate dynamically.
        // The DB has `earnings` on Professional but that implies total life-time. 
        // We'll calculate periodically from Bookings to be safe, or just use price * (1 - comm/100))

        $commissionRate = (100 - $professional->commission) / 100;

        $todaysEarnings = Booking::where('professional_id', $professional->id)
            ->where('status', 'Completed')
            ->where('date', $today)
            ->sum(DB::raw("price * {$commissionRate}"));

        $weeklyEarnings = Booking::where('professional_id', $professional->id)
            ->where('status', 'Completed')
            ->whereBetween('date', [$startOfWeek, $today])
            ->sum(DB::raw("price * {$commissionRate}"));

        $monthlyEarnings = Booking::where('professional_id', $professional->id)
            ->where('status', 'Completed')
            ->whereBetween('date', [$startOfMonth, $today])
            ->sum(DB::raw("price * {$commissionRate}"));

        $totalJobs = Booking::where('professional_id', $professional->id)
            ->where('status', 'Completed')
            ->count();

        $activeJobsAssigned = Booking::where('professional_id', $professional->id)
            ->whereIn('status', ['Unassigned', 'Pending', 'Confirmed', 'Assigned'])
            ->count();

        $activeJobsInProgress = Booking::where('professional_id', $professional->id)
            ->whereIn('status', ['Started', 'In Progress'])
            ->count();

        return $this->success([
            'overall_earnings' => $professional->earnings ?? 0,
            'total_hours'      => 0, // Mock for now until time tracking is added
            'total_jobs'       => $totalJobs,
            'summary'          => [
                'today'      => round($todaysEarnings, 2),
                'this_week'  => round($weeklyEarnings, 2),
                'this_month' => round($monthlyEarnings, 2),
            ],
            'active_jobs'      => [
                'assigned'    => $activeJobsAssigned,
                'in_progress' => $activeJobsInProgress,
            ],
        ], 'Earnings overview retrieved.');
    }

    /**
     * GET /api/professionals/jobs/history
     * History of completed jobs and associated payouts
     */
    public function history(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $jobs = Booking::where('professional_id', $professional->id)
            ->where('status', 'Completed')
            ->orderBy('date', 'desc')
            ->orderBy('slot', 'desc')
            ->paginate(15);

        return $this->success($jobs, 'Job history retrieved.');
    }

    /**
     * GET /api/professionals/wallet
     * Current wallet balance and transaction history
     */
    public function wallet(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $tab = $request->query('tab', 'earnings'); // 'earnings' or 'coins'
        $type = $tab === 'coins' ? 'coin' : 'cash';

        $cashWallet = Wallet::firstOrCreate(
            ['holder_type' => 'professional', 'holder_id' => $professional->id, 'type' => 'cash'],
            ['balance' => 0]
        );

        $coinWallet = Wallet::firstOrCreate(
            ['holder_type' => 'professional', 'holder_id' => $professional->id, 'type' => 'coin'],
            ['balance' => 0]
        );

        $activeWallet = $type === 'coin' ? $coinWallet : $cashWallet;

        $transactions = WalletTransaction::where('wallet_id', $activeWallet->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($t) use ($type) {
                // UI Formatting logic
                $title = $t->description ?: 'Earnings Payout';
                if ($t->source === 'withdrawal') {
                    $title = 'Successful Withdrawal';
                }

                $subtitle = $t->created_at->isToday() 
                    ? 'Today, ' . $t->created_at->format('g:i A')
                    : ($t->created_at->isYesterday() 
                        ? 'Yesterday, ' . $t->created_at->format('g:i A') 
                        : $t->created_at->format('d M, g:i A'));

                $val = $type === 'coin' ? $t->amount : ($t->amount / 100);
                $prefix = $t->type === 'debit' ? '-' : '+';
                $currency = $type === 'coin' ? '' : '₹';
                $formattedAmount = "{$prefix}{$currency}" . number_format($val, 0);

                return [
                    'id'             => $t->id,
                    'title'          => $title,
                    'subtitle'       => $subtitle,
                    'amount'         => $val,
                    'display_amount' => $formattedAmount,
                    'type'           => $t->type,
                    'created_at'     => $t->created_at,
                ];
            });

        return $this->success([
            'cash_balance'   => $cashWallet->balance / 100,
            'coin_balance'   => $coinWallet->balance, // Coins shouldn't be divided by 100 ordinarily
            'active_balance' => $type === 'coin' ? $coinWallet->balance : ($cashWallet->balance / 100),
            'transactions'   => $transactions
        ], 'Wallet retrieved.');
    }

    /**
     * POST /api/professionals/wallet/withdraw
     * Request a payout/withdrawal
     */
    public function withdraw(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $validated = $request->validate([
            'amount' => 'required|numeric|min:100', // e.g. minimum withdrawal ₹100
        ]);

        $amountInPaise = (int) ($validated['amount'] * 100);

        return DB::transaction(function () use ($professional, $amountInPaise) {
            $wallet = Wallet::where('holder_type', 'professional')
                ->where('holder_id', $professional->id)
                ->where('type', 'cash')
                ->lockForUpdate()
                ->first();

            if (!$wallet || $wallet->balance < $amountInPaise) {
                return $this->error('Insufficient wallet balance.', 400);
            }

            // Deduct balance
            $wallet->balance -= $amountInPaise;
            $wallet->save();

            // Record transaction
            WalletTransaction::create([
                'wallet_id'     => $wallet->id,
                'type'          => 'debit',
                'amount'        => $amountInPaise,
                'balance_after' => $wallet->balance,
                'source'        => 'withdrawal',
                'description'   => 'Withdrawal to bank account',
            ]);

            return $this->success([
                'balance' => $wallet->balance / 100
            ], 'Withdrawal successful.');
        });
    }
}
