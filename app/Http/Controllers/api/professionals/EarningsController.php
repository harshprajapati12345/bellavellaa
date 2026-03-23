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
            'id' => $t->id,
            'title' => $title,
            'subtitle' => $subtitle,
            'amount' => $val,
            'display_amount' => $formattedAmount,
            'type' => $t->type,
            'created_at' => $t->created_at,
            ];
        });

        $today = Carbon::today()->toDateString();
        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $commissionRate = (100 - ($professional->commission ?? 0)) / 100;

        $weeklyEarnings = Booking::where('professional_id', $professional->id)
            ->where('status', 'completed')
            ->whereBetween('date', [$startOfWeek, $today])
            ->sum(DB::raw("price * {$commissionRate}"));

        $monthlyEarnings = Booking::where('professional_id', $professional->id)
            ->where('status', 'completed')
            ->whereBetween('date', [$startOfMonth, $today])
            ->sum(DB::raw("price * {$commissionRate}"));

        $totalJobs = Booking::where('professional_id', $professional->id)
            ->where('status', 'completed')
            ->count();

        $depositAmountPaise = WalletTransaction::where('wallet_id', $cashWallet->id)
            ->where('source', 'deposit')
            ->where('type', 'credit')
            ->sum('amount');

        $withdrawnDepositPaise = WalletTransaction::where('wallet_id', $cashWallet->id)
            ->where('source', 'withdrawal')
            ->where('reference_type', 'deposit') // Assuming we track this
            ->sum('amount');

        // For now, simpler: anything from 'deposit' source is deposit.
        // Everything else is earnings.
        $totalDepositPaise = WalletTransaction::where('wallet_id', $cashWallet->id)
            ->where('source', 'deposit')
            ->sum(DB::raw('CASE WHEN type = "credit" THEN amount ELSE -amount END'));

        $totalCashBalancePaise = $cashWallet->balance;
        $depositBalancePaise = max(0, $totalDepositPaise);
        $earningsBalancePaise = max(0, $totalCashBalancePaise - $depositBalancePaise);

        $todayEarnings = Booking::where('professional_id', $professional->id)
            ->where('status', 'completed')
            ->where('date', $today)
            ->sum(DB::raw("price * {$commissionRate}"));

        return $this->success([
            'cash_balance' => $totalCashBalancePaise / 100,
            'earnings_balance' => $earningsBalancePaise / 100,
            'deposit_balance' => $depositBalancePaise / 100,
            'total_balance' => $totalCashBalancePaise / 100,
            'coin_balance' => $professional->coins_balance, // New direct column
            'coins_balance' => $professional->coins_balance, // For Flutter compatibility
            'kit_count' => \App\Models\KitOrder::where('professional_id', $professional->id)->sum('quantity'),
            'active_balance' => $type === 'coin' ? $professional->coins_balance : ($totalCashBalancePaise / 100),
            'transactions' => $transactions,
            'today_earnings' => $todayEarnings,
            'weekly_earnings' => $weeklyEarnings,
            'monthly_earnings' => $monthlyEarnings,
            'total_jobs' => $professional->total_completed_jobs, // Use new safe column
            'total_completed_jobs' => $professional->total_completed_jobs, // For Flutter
            'kit_orders' => \App\Models\KitOrder::with('product')
            ->where('professional_id', $professional->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
            return [
                    'id' => $order->id,
                    'title' => $order->product->name ?? 'Service Kit',
                    'quantity' => $order->quantity,
                    'amount' => (double)$order->quantity,
                    'status' => $order->status,
                    'type' => 'credit',
                    'created_at' => $order->created_at->format('d M, Y'),
                    'description' => "Assigned " . $order->quantity . " kits",
                ];
        }),
        ], 'Wallet retrieved.');
    }

    /**
     * POST /api/professional/request-withdrawal
     */
    public function requestWithdrawal(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'amount' => 'required|numeric|min:100', // Minimum ₹100
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $amountInPaise = (int)($request->amount * 100);

        return DB::transaction(function () use ($professional, $amountInPaise, $request) {
            $wallet = Wallet::where('holder_type', 'professional')
                ->where('holder_id', $professional->id)
                ->where('type', 'cash')
                ->lockForUpdate()
                ->first();

            if (!$wallet || $wallet->balance < $amountInPaise) {
                return $this->error('Insufficient wallet balance.', 400);
            }

            // Create withdrawal request record (PENDING)
            $withdrawal = \App\Models\WithdrawalRequest::create([
                'professional_id' => $professional->id,
                'amount' => $amountInPaise,
                'method' => 'direct',
                'status' => \App\Models\WithdrawalRequest::STATUS_PENDING,
                'transaction_reference' => 'PENDING-' . strtoupper(Str::random(8)),
            ]);

            // Debit the wallet (Deduction on request - Option B)
            $wallet->debit(
                $amountInPaise,
                'withdrawal_request',
                "Withdrawal request of ₹" . $request->amount . " (Pending)",
                $withdrawal->id,
                \App\Models\WithdrawalRequest::class
            );

            return $this->success([
                'balance' => $wallet->balance / 100
            ], 'Withdrawal request submitted for approval.');
        });
    }

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
