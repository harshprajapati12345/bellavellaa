<?php

namespace App\Http\Controllers\Api\Professionals;

use App\Models\Referral;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralController extends BaseController
{
    /**
     * GET /api/professional/referrals
     */
    public function index(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');
        $referrals = Referral::where('referrer_id', $professional->id)
            ->where('referrer_type', 'professional')
            ->with(['referred'])
            ->latest()
            ->get();

        if (empty($professional->referral_code)) {
            $professional->referral_code = \App\Models\Professional::generateUniqueReferralCode($professional->name);
            $professional->save();
        }

        $stats = [
            'referral_code' => $professional->referral_code,
            'total_referrals' => $referrals->where('status', 'success')->count(),
            'total_earnings' => $referrals->where('status', 'success')->sum('reward_amount'),
            'pending_referrals' => $referrals->where('status', 'pending')->count(),
            'referrer_reward' => \App\Models\RewardRule::where('type', 'referrer')->where('status', true)->value('coins') ?? 0,
            'referred_reward' => \App\Models\RewardRule::where('type', 'referred_user')->where('status', true)->value('coins') ?? 0,
            'history' => $referrals->map(function ($ref) {
                return [
                    'id' => $ref->id,
                    'phone' => $ref->referred_phone,
                    'status' => $ref->status,
                    'amount' => $ref->reward_amount,
                    'date' => $ref->created_at->format('d M Y'),
                    'referred_name' => $ref->referred->name ?? 'Unknown',
                ];
            })
        ];

        return $this->success($stats, 'Referral stats retrieved.');
    }

    /**
     * POST /api/professional/referrals/submit
     * (Optional: if the professional needs to manually enter a phone number they referred)
     */
    public function submit(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $validated = $request->validate([
            'phone' => 'required|string',
        ]);

        // Check if already referred
        $exists = Referral::where('referred_phone', $validated['phone'])->exists();
        if ($exists) {
            return $this->error('This phone number is already referred.', 422);
        }

        $referral = Referral::create([
            'referrer_id' => $professional->id,
            'referred_phone' => $validated['phone'],
            'status' => 'pending',
        ]);

        return $this->success($referral, 'Referral submitted.');
    }
}
