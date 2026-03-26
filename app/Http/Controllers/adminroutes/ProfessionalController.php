<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Professional;
use Illuminate\Http\Request;

class ProfessionalController extends Controller
{
    public function index()
    {
        $professionals = Professional::all();
        $total = $professionals->count();
        $active = $professionals->where('status', 'Active')->count();
        $online = $professionals->where('last_seen', '>=', now()->subMinutes(30))->count();
        $topPro = $professionals->sortByDesc('bookings_count')->first();

        return view('professionals.index', compact('professionals', 'total', 'active', 'online', 'topPro'));
    }

    public function create()
    {
        return view('professionals.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $stored = $request->file('avatar')->store('professionals', 'public');
            $avatarPath = asset('storage/' . $stored);
        }

        Professional::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'category' => $request->category,
            'city' => $request->city,
            'bio' => $request->bio,
            'experience' => $request->experience ?? '0 years',
            'rating' => $request->rating ?? 0,
            'status' => $request->has('status') ? 'Active' : 'Suspended',
            'avatar' => $avatarPath,
            'joined' => now(),
        ]);

        return redirect()->route('professionals.index')->with('success', 'Professional added!');
    }

    public function show(Professional $professional)
    {
        return response()->json([
            'id' => $professional->id,
            'name' => $professional->name,
            'specialty' => $professional->specialty,
            'experience' => $professional->experience . ' Years',
            'status' => $professional->verification_status,
            'phone' => $professional->phone ?? '-',
            'email' => $professional->email ?? '-',
            'city' => $professional->city ?? '-',
            'joined' => \Carbon\Carbon::parse($professional->created_at)->format('d M Y'),
            'avatar' => $professional->avatar ? asset('storage/' . $professional->avatar) : 'https://i.pravatar.cc/150?u=' . $professional->id,
        ]);
    }

    public function edit(Professional $professional)
    {
        return view('professionals.edit', compact('professional'));
    }

    public function update(Request $request, Professional $professional)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $avatarPath = $professional->avatar;
        if ($request->hasFile('avatar')) {
            $stored = $request->file('avatar')->store('professionals', 'public');
            $avatarPath = asset('storage/' . $stored);
        }

        $professional->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'category' => $request->category,
            'city' => $request->city,
            'bio' => $request->bio,
            'experience' => $request->experience ?? $professional->experience,
            'rating' => $request->rating ?? $professional->rating,
            'status' => $request->has('status') ? 'Active' : 'Suspended',
            'avatar' => $avatarPath,
        ]);

        return redirect()->route('professionals.index')->with('success', 'Professional updated!');
    }

    public function verification()
    {
        $requests = Professional::where('verification', '!=', 'Verified')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'avatar' => $p->avatar ?: 'https://i.pravatar.cc/150?u=' . $p->id,
                    'aadhaar' => $p->aadhaar ?? '-',
                    'pan' => $p->pan ?? '-',
                    'submitted' => $p->updated_at->format('Y-m-d'),
                    'status' => $p->verification,
                ];
            });

        $pendingCount = $requests->where('status', 'Pending')->count();
        $approvedCount = $requests->where('status', 'Verified')->count();
        $rejectedCount = $requests->where('status', 'Rejected')->count();

        return view('professionals.verification.index', compact('requests', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    public function verificationReview($id)
    {
        $professional = Professional::findOrFail($id);

        $languages = $this->normalizeList($professional->languages);
        $skills = $this->normalizeList($professional->category);

        $req = [
            'id' => $professional->id,
            'name' => $professional->name,
            'avatar' => $professional->avatar ?: 'https://i.pravatar.cc/150?u=' . $professional->id,
            'email' => $professional->email ?? '-',
            'phone' => $professional->phone ?? '-',
            'gender' => $professional->gender ?? '-',
            'dob' => $professional->dob,
            'experience' => $professional->experience ?? '-',
            'skills' => $skills,
            'languages' => $languages,
            'address' => $professional->service_area ?? '-',
            'city' => $professional->city ?? '-',
            'state' => $professional->state ?? '-',
            'pincode' => $professional->pincode ?? '-',
            'aadhaar' => $professional->aadhaar ?? '-',
            'pan' => $professional->pan ?? '-',
            'submitted' => $professional->updated_at->format('Y-m-d H:i:s'),
            'status' => $professional->verification ?: 'Pending',
            'aadhaar_front' => $professional->aadhaar_front,
            'aadhaar_back' => $professional->aadhaar_back,
            'pan_img' => $professional->pan_img,
            'certificate_img' => $professional->certificate_img,
            'light_bill' => $professional->light_bill,
            'selfie' => $professional->selfie ?: $professional->avatar,
        ];

        return view('professionals.verification.review', compact('req', 'professional'));
    }

    public function approveVerification($id)
    {
        $professional = Professional::findOrFail($id);
        $professional->update(['verification' => 'Verified']);

        return redirect()->route('professionals.verification')->with('success', 'Professional verified successfully!');
    }

    public function rejectVerification(Request $request, $id)
    {
        $professional = Professional::findOrFail($id);
        $professional->update(['verification' => 'Rejected']);

        return redirect()->route('professionals.verification')->with('error', 'Professional verification rejected.');
    }

    public function requestVerificationChanges(Request $request, $id)
    {
        $professional = Professional::findOrFail($id);
        $professional->update(['verification' => 'Pending']);

        return redirect()->route('professionals.verification')->with('info', 'Change request sent to professional.');
    }

    public function orders()
    {
        $orders = \App\Models\Booking::with(['customer', 'professional'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($b) {
                return [
                    'id' => 'ORD-' . (1000 + $b->id),
                    'customer' => $b->customer->name ?? 'Unknown',
                    'customer_phone' => $b->customer->phone ?? '-',
                    'professional' => $b->professional->name ?? '-',
                    'service' => $b->service_name ?? '-',
                    'date' => $b->date,
                    'time' => $b->slot,
                    'amount' => $b->price ?? 0,
                    'commission' => $b->commission ?? 0,
                    'pro_earning' => ($b->price ?? 0) - ($b->commission ?? 0),
                    'order_status' => $b->status,
                    'payment_status' => $b->payment_status ?? 'Pending',
                    'address' => $b->address ?? '-',
                    'payment_method' => $b->payment_method ?? '-',
                ];
            })->all();

        return view('professionals.orders.index', compact('orders'));
    }

    public function history()
    {
        $history = Professional::withCount([
            'bookings as completed' => function ($q) {
                $q->where('status', 'completed');
            },
            'bookings as cancelled' => function ($q) {
                $q->where('status', 'cancelled');
            },
        ])
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'avatar' => $p->avatar ?: 'https://i.pravatar.cc/150?u=' . $p->id,
                    'category' => $p->category ?? 'Prime',
                    'total_orders' => $p->orders,
                    'completed' => $p->completed ?? 0,
                    'cancelled' => $p->cancelled ?? 0,
                    'total_earnings' => $p->earnings,
                    'total_commission' => ($p->earnings * $p->commission / 100),
                    'rating' => $p->rating,
                    'payout_status' => 'paid',
                    'monthly' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    'reviews' => [],
                ];
            })->all();

        return view('professionals.history.index', compact('history'));
    }

    public function deposits()
    {
        $transactions = \App\Models\WalletTransaction::with(['wallet.holder'])
            ->whereHas('wallet', function ($q) {
                $q->where('holder_type', 'professional')->where('type', 'cash');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($t) {
                $amountBase = $t->amount;
                $balanceBase = $t->balance_after;

                return [
                    'id' => 'TRX-' . (1000 + $t->id),
                    'professional' => $t->wallet->holder->name ?? 'Unknown',
                    'pro_id' => $t->wallet->holder->id ?? '-',
                    'date' => $t->created_at->format('Y-m-d H:i:s'),
                    'type' => ucfirst($t->type),
                    'amount' => $amountBase / 100,
                    'balance_after' => $balanceBase / 100,
                    'source' => $t->source,
                    'description' => $t->description,
                ];
            });

        $totalDeposits = $transactions->where('type', 'Credit')->sum('amount');
        $totalWithdrawals = $transactions->where('type', 'Debit')->sum('amount');

        return view('professionals.deposits.index', compact('transactions', 'totalDeposits', 'totalWithdrawals'));
    }

    public function suspend($id)
    {
        $pro = Professional::findOrFail($id);
        $pro->update(['status' => 'Suspended']);

        return back()->with('success', 'Professional suspended.');
    }

    public function activate($id)
    {
        $pro = Professional::findOrFail($id);
        $pro->update(['status' => 'Active']);

        return back()->with('success', 'Professional activated.');
    }

    public function destroy(Professional $professional)
    {
        $professional->delete();

        return redirect()->route('professionals.index')->with('success', 'Professional removed.');
    }

    private function normalizeList($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('trim', $value)));
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return array_values(array_filter(array_map('trim', $decoded)));
            }

            return array_values(array_filter(array_map('trim', explode(',', $value))));
        }

        return [];
    }
}
