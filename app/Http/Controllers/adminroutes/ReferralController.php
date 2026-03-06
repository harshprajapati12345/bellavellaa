<?php

namespace App\Http\Controllers\adminroutes;

use Illuminate\Http\Request;

class ReferralController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $referrals = \App\Models\Referral::with(['referrer', 'referred'])->latest()->paginate(20);
        
        $stats = [
            'total_coins' => \App\Models\WalletTransaction::where('source', 'referral_reward')->sum('amount') 
                           + \App\Models\WalletTransaction::where('source', 'referral_signup')->sum('amount'),
            'total_referrals' => \App\Models\Referral::where('status', 'success')->count(),
            'top_referrer' => \App\Models\Referral::select('referrer_id', 'referrer_type', \DB::raw('count(*) as count'))
                                ->where('status', 'success')
                                ->groupBy('referrer_id', 'referrer_type')
                                ->orderByDesc('count')
                                ->first()
        ];

        return view('referrals.index', compact('referrals', 'stats'));
    }

    public function toggleStatus(Request $request, $id)
    {
        $referral = \App\Models\Referral::findOrFail($id);
        $referral->update(['status' => $request->status]);
        
        return back()->with('success', 'Referral status updated to ' . $request->status);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
