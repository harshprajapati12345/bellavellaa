<?php

namespace App\Http\Controllers\Api\Professionals;

use App\Models\Booking;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Http\Resources\Api\BookingResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            ->where('status', 'completed')
            ->where('date', $today)
            ->get()
            ->sum(fn($b) => $b->price - ($b->commission ?? ($b->price * $professional->commission / 100)));

        $weeklyEarnings = Booking::where('professional_id', $professional->id)
            ->where('status', 'completed')
            ->whereBetween('date', [$startOfWeek, $today])
            ->get()
            ->sum(fn($b) => $b->price - ($b->commission ?? ($b->price * $professional->commission / 100)));

        $monthlyEarnings = Booking::where('professional_id', $professional->id)
            ->where('status', 'completed')
            ->whereBetween('date', [$startOfMonth, $today])
            ->get()
            ->sum(fn($b) => $b->price - ($b->commission ?? ($b->price * $professional->commission / 100)));

        $totalJobs = Booking::where('professional_id', $professional->id)
            ->where('status', 'completed')
            ->count();

        $activeJobsAssigned = Booking::where('professional_id', $professional->id)
            ->whereIn('status', ['unassigned', 'pending', 'confirmed', 'assigned'])
            ->count();

        $activeJobsInProgress = Booking::where('professional_id', $professional->id)
            ->whereIn('status', ['started', 'in_progress'])
            ->count();

        return $this->success([
            'overall_earnings' => $professional->earnings ?? 0,
            'total_hours' => 0, // Mock for now until time tracking is added
            'total_jobs' => $totalJobs,
            'summary' => [
                'today' => round($todaysEarnings, 2),
                'this_week' => round($weeklyEarnings, 2),
                'this_month' => round($monthlyEarnings, 2),
            ],
            'active_jobs' => [
                'assigned' => $activeJobsAssigned,
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
            ->where('status', 'completed')
            ->orderBy('date', 'desc')
            ->orderBy('slot', 'desc')
            ->paginate(15);

        $jobs->getCollection()->transform(function ($job) {
            return new BookingResource($job);
        });

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
                    'id' => $t->id,
                    'title' => $title,
                    'subtitle' => $subtitle,
                    'amount' => $val,
                    'display_amount' => $formattedAmount,
                    'type' => $t->type,
                    'created_at' => $t->created_at,
                ];
            });

        $withdrawDelayDays = (int) (\App\Models\Setting::get('withdraw_delay_days') ?? 7);
        $nextAllowed = $professional->last_withdrawal_at 
            ? $professional->last_withdrawal_at->copy()->addDays($withdrawDelayDays) 
            : null;

        $totalCashBalancePaise = (int) $cashWallet->balance;

        // --- Ground Truth Balance Logic ---
        // 1. Matured Balance: Earnings credits that are older than X days.
        $maturedBalancePaise = (int) $cashWallet->transactions()
            ->where('source', 'earnings')
            ->where('type', 'credit')
            ->matured($withdrawDelayDays)
            ->sum('amount');
        
        // 2. Locked Balance: Earnings credits created within the cooldown period.
        $lockedBalancePaise = (int) $cashWallet->transactions()
            ->where('source', 'earnings')
            ->where('type', 'credit')
            ->where('created_at', '>', now()->subDays($withdrawDelayDays))
            ->sum('amount');

        // 3. Deposit Balance: All manual top-ups/deposits (always available).
        $depositBalancePaise = (int) $cashWallet->transactions()
            ->where('source', 'deposit')
            ->sum(DB::raw('CASE WHEN type = "credit" THEN amount ELSE -amount END'));
        
        // 4. Available to Withdraw: Deposit + Matured Earnings (capped by total balance).
        $availableToWithdrawPaise = min($totalCashBalancePaise, max(0, $depositBalancePaise + $maturedBalancePaise));

        $today = Carbon::today()->toDateString();
        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $commissionRate = (100 - ($professional->commission ?? 0)) / 100;

        $todayEarnings = (float) Booking::where('professional_id', $professional->id)
            ->where('status', 'completed')
            ->where('date', $today)
            ->get()
            ->sum(fn($b) => $b->price * $commissionRate);

        $weeklyEarnings = (float) Booking::where('professional_id', $professional->id)
            ->where('status', 'completed')
            ->whereBetween('date', [$startOfWeek, $today])
            ->get()
            ->sum(fn($b) => $b->price * $commissionRate);

        $monthlyEarnings = (float) Booking::where('professional_id', $professional->id)
            ->where('status', 'completed')
            ->whereBetween('date', [$startOfMonth, $today])
            ->get()
            ->sum(fn($b) => $b->price * $commissionRate);

        return $this->success([
            'cash_balance' => $totalCashBalancePaise / 100,
            'available_balance' => $availableToWithdrawPaise / 100,
            'locked_balance' => $lockedBalancePaise / 100,
            'deposit_balance' => max(0, $depositBalancePaise) / 100,
            'earnings_balance' => ($totalCashBalancePaise - max(0, $depositBalancePaise)) / 100,
            'total_balance' => $totalCashBalancePaise / 100,
            'withdraw_delay_days' => $withdrawDelayDays,
            'can_withdraw' => (!$nextAllowed || now()->gte($nextAllowed)) && ($availableToWithdrawPaise >= 10000), // Min ₹100
            'next_withdrawal_at' => $nextAllowed ? $nextAllowed->toIso8601String() : null,
            'remaining_seconds' => $nextAllowed ? max(0, now()->diffInSeconds($nextAllowed, false)) : 0,
            'coin_balance' => (int) $professional->coins_balance,
            'coins_balance' => (int) $professional->coins_balance,
            'active_balance' => $type === 'coin' ? $professional->coins_balance : ($totalCashBalancePaise / 100),
            'transactions' => $transactions,
            'today_earnings' => round($todayEarnings, 2),
            'weekly_earnings' => round($weeklyEarnings, 2),
            'monthly_earnings' => round($monthlyEarnings, 2),
            'total_completed_jobs' => $professional->total_completed_jobs ?? 0,
            'kit_orders' => \App\Models\KitOrder::with('product')
                ->where('professional_id', $professional->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn($o) => [
                    'id' => $o->id,
                    'title' => $o->product->name ?? 'Service Kit',
                    'quantity' => $o->quantity,
                    'status' => $o->status,
                    'created_at' => $o->created_at->format('d M, Y'),
                ]),
        ], 'Wallet overview retrieved.');
    }

    // Removing redundant requestWithdrawal as it's moved to WithdrawalController

    /**
     * POST /api/professional/wallet/deposit/create-order
     */
    public function createDepositOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $amountInPaise = (int)round($validated['amount'] * 100);

        try {
            if (config('services.razorpay.mock')) {
                return $this->success([
                    'order_id' => 'order_mock_' . strtolower(Str::random(14)),
                    'amount' => $amountInPaise,
                    'amount_inr' => $validated['amount'],
                    'currency' => 'INR',
                    'receipt' => 'dep_mock_' . Str::random(8),
                    'is_mock' => true,
                ], 'Razorpay mock deposit order created.');
            }

            $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            $order = $api->order->create([
                'receipt' => 'dep_' . Str::random(8),
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'notes' => [
                    'professional_id' => $request->user('professional-api')->id,
                    'type' => 'wallet_deposit'
                ]
            ]);

            return $this->success([
                'order_id' => $order['id'],
                'amount' => $amountInPaise,
                'amount_inr' => $validated['amount'],
                'currency' => 'INR',
                'receipt' => $order['receipt'],
            ], 'Deposit order created.');
        }
        catch (\Exception $e) {
            return $this->error('Failed to create Razorpay order: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/professional/wallet/deposit/verify
     */
    public function verifyDeposit(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $amountInPaise = (int)round($validated['amount'] * 100);

        try {
            if (!config('services.razorpay.mock')) {
                $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
                $attributes = [
                    'razorpay_order_id' => $validated['razorpay_order_id'],
                    'razorpay_payment_id' => $validated['razorpay_payment_id'],
                    'razorpay_signature' => $validated['razorpay_signature']
                ];
                $api->utility->verifyPaymentSignature($attributes);
            }
        }
        catch (\Exception $e) {
            return $this->error('Payment verification failed: ' . $e->getMessage(), 400);
        }

        return DB::transaction(function () use ($professional, $amountInPaise, $validated) {
            $wallet = Wallet::firstOrCreate(
            ['holder_type' => 'professional', 'holder_id' => $professional->id, 'type' => 'cash'],
            ['balance' => 0]
            );

            $wallet->credit(
                $amountInPaise,
                'deposit',
                'Wallet deposit via Razorpay',
                $validated['razorpay_payment_id'],
                'razorpay_payment'
            );

            return $this->success([
                'balance' => $wallet->balance / 100
            ], 'Deposit successful.');
        });
    }

    /**
     * GET /api/professional/schedule
     */
    public function schedule(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        // Upcoming bookings
        $schedule = Booking::where('professional_id', $professional->id)
            ->where('date', '>=', Carbon::today()->toDateString())
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->orderBy('date', 'asc')
            ->orderBy('slot', 'asc')
            ->get();

        return $this->success(BookingResource::collection($schedule), 'Schedule retrieved.');
    }
}
