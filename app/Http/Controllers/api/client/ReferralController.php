<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Professionals\BaseController;
use App\Models\Referral;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralController extends BaseController
{
    /**
     * GET /api/client/referrals
     */
    public function index(Request $request): JsonResponse
    {
        $customer = $request->user('api');
        
        $referrals = Referral::where('referrer_id', $customer->id)
            ->where('referred_type', 'client') // or both if allowed
            ->with(['referred'])
            ->latest()
            ->get();

        $stats = [
            'referral_code' => $customer->referral_code,
            'total_referrals' => $referrals->where('status', 'success')->count(),
            'total_earnings' => $referrals->where('status', 'success')->sum('reward_amount'),
            'pending_referrals' => $referrals->where('status', 'pending')->count(),
            'history' => $referrals->map(function ($ref) {
                return [
                    'id' => $ref->id,
                    'phone' => $ref->referred_phone,
                    'status' => $ref->status,
                    'amount' => $ref->reward_amount,
                    'date' => $ref->created_at->format('d M Y'),
                    'referred_name' => $ref->referred->name ?? 'User',
                ];
            })
        ];

        return $this->success($stats, 'Referral stats retrieved.');
    }
}
