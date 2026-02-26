<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Professional;
use Illuminate\Http\Request;

class ProfessionalController extends Controller
{
    public function index()
    {
        $professionals = Professional::all();
        $total   = $professionals->count();
        $active  = $professionals->where('status', 'Active')->count();
        $topPro  = $professionals->sortByDesc('bookings_count')->first();

        return view('professionals.index', compact('professionals', 'total', 'active', 'topPro'));
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
            'name'       => $request->name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'category'   => $request->category,
            'city'       => $request->city,
            'bio'        => $request->bio,
            'experience' => $request->experience ?? '0 years',
            'rating'     => $request->rating ?? 0,
            'status'     => $request->has('status') ? 'Active' : 'Suspended',
            'avatar'     => $avatarPath,
            'joined'     => now(),
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
            'phone' => $professional->phone ?? '—',
            'email' => $professional->email ?? '—',
            'city' => $professional->city ?? '—',
            'joined' => \Carbon\Carbon::parse($professional->created_at)->format('d M Y'),
            'avatar' => $professional->avatar ? asset('storage/'.$professional->avatar) : 'https://i.pravatar.cc/150?u='.$professional->id,
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
            'name'       => $request->name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'category'   => $request->category,
            'city'       => $request->city,
            'bio'        => $request->bio,
            'experience' => $request->experience ?? $professional->experience,
            'rating'     => $request->rating ?? $professional->rating,
            'status'     => $request->has('status') ? 'Active' : 'Suspended',
            'avatar'     => $avatarPath,
        ]);

        return redirect()->route('professionals.index')->with('success', 'Professional updated!');
    }

    public function verification()
    {
        // Fetch real verification requests (pending docs or specifically flagged)
        $requests = Professional::where('verification', '!=', 'Verified')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'avatar' => $p->avatar ?: 'https://i.pravatar.cc/150?u='.$p->id,
                    'aadhaar' => $p->aadhaar ?? '—',
                    'pan' => $p->pan ?? '—',
                    'submitted' => $p->updated_at->format('Y-m-d'),
                    'status' => $p->verification,
                ];
            });

        $pendingCount  = $requests->where('status', 'Pending')->count();
        $approvedCount = $requests->where('status', 'Verified')->count();
        $rejectedCount = $requests->where('status', 'Rejected')->count();

        return view('professionals.verification.index', compact('requests', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    public function verificationReview($id)
    {
        $professional = Professional::findOrFail($id);
        
        // Mocking additional fields if they don't exist in DB yet to match legacy UI
        $req = [
            'id' => $professional->id,
            'name' => $professional->name,
            'avatar' => $professional->avatar ?: 'https://i.pravatar.cc/150?u='.$professional->id,
            'email' => $professional->email ?? 'anjali.m@example.com',
            'phone' => $professional->phone ?? '+91 98765 43210',
            'aadhaar' => $professional->aadhaar ?? 'XXXX XXXX 4321',
            'pan' => $professional->pan ?? 'ABCDE1234F',
            'submitted' => $professional->updated_at->format('Y-m-d H:i:s'),
            'status' => $professional->verification ?: 'Pending',
            'aadhaar_front' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=600&q=80',
            'aadhaar_back' => 'https://images.unsplash.com/photo-1554224154-26032ffc0d07?auto=format&fit=crop&w=600&q=80',
            'pan_img' => 'https://images.unsplash.com/photo-1554224155-1a8a10e29e4c?auto=format&fit=crop&w=600&q=80',
            'selfie' => $professional->avatar ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80'
        ];

        return view('professionals.verification.review', compact('req', 'professional'));
    }

    public function orders()
    {
        // Using real bookings with patient/professional details
        $orders = \App\Models\Booking::with(['user', 'professional'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($b) {
                return [
                    'id' => 'ORD-' . (1000 + $b->id),
                    'customer' => $b->user->name ?? 'Unknown',
                    'customer_phone' => $b->user->phone ?? '—',
                    'professional' => $b->professional->name ?? '—',
                    'service' => $b->service_name ?? '—',
                    'date' => $b->date,
                    'time' => $b->time,
                    'amount' => $b->total_price ?? 0,
                    'commission' => $b->commission ?? 0,
                    'pro_earning' => ($b->total_price ?? 0) - ($b->commission ?? 0),
                    'order_status' => $b->status,
                    'payment_status' => $b->payment_status ?? 'Pending',
                    'address' => $b->address ?? '—',
                    'payment_method' => $b->payment_method ?? '—',
                ];
            });

        return view('professionals.orders.index', compact('orders'));
    }

    public function history()
    {
        $history = Professional::withCount(['bookings as completed' => function($q) {
                $q->where('status', 'Completed');
            }, 'bookings as cancelled' => function($q) {
                $q->where('status', 'Cancelled');
            }])
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'avatar' => $p->avatar ?: 'https://i.pravatar.cc/150?u='.$p->id,
                    'category' => $p->category ?? 'Prime',
                    'total_orders' => $p->orders,
                    'completed' => $p->completed ?? 0,
                    'cancelled' => $p->cancelled ?? 0,
                    'total_earnings' => $p->earnings,
                    'total_commission' => ($p->earnings * $p->commission / 100),
                    'rating' => $p->rating,
                    'payout_status' => 'Paid', // Placeholder for advanced payout logic
                    'monthly' => [0,0,0,0,0,0,0,0,0,0,0,0], // Real charts would need complex grouping
                    'reviews' => [],
                ];
            });

        return view('professionals.history.index', compact('history'));
    }

    public function destroy(Professional $professional)
    {
        $professional->delete();
        return redirect()->route('professionals.index')->with('success', 'Professional removed.');
    }
}
