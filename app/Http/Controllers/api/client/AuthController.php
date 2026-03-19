<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Requests\Api\SendOtpRequest;
use App\Http\Requests\Api\VerifyOtpRequest;
use App\Http\Resources\Api\CustomerResource;
use App\Models\Customer;
use App\Models\Otp;
use App\Services\RewardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends BaseController
{
    // ─── Guard helper ──────────────────────────────────────────────

    protected function guard()
    {
        return Auth::guard('api');
    }

    // ─── 1. SEND OTP ───────────────────────────────────────────────

    /**
     * POST /api/flutter/auth/send-otp
     *
     * Generates a 4-digit OTP, invalidates any previous unsent OTPs
     * for the same mobile, and (in production) dispatches it via SMS.
     */
    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $otp = Otp::generate($request->mobile, 'login', 5, 4);

        // TODO: integrate SMS gateway
        // SmsService::send($request->mobile, "Your BellaVella OTP is {$otp->otp}");

        $data = [
            'expires_in' => 300, // 5 minutes in seconds
        ];

        // Show OTP in non-production environments for testing
        if (!app()->isProduction()) {
            $data['otp_debug'] = $otp->otp;
            $data['otp'] = $otp->otp; // For Flutter auto-fill in local/dev
        }

        return $this->success($data, 'OTP sent successfully.');
    }

    // ─── 2. VERIFY OTP & ISSUE JWT ─────────────────────────────────

    /**
     * POST /api/flutter/auth/verify-otp
     *
     * Verifies the OTP, finds or creates the customer,
     * and returns a JWT access token.
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $otpRecord = Otp::verify($request->mobile, $request->otp, 'login');

        if (!$otpRecord) {
            return $this->error('Invalid or expired OTP.', 401);
        }

        // Find or create customer on first login
        $isNewUser = !Customer::where('mobile', $request->mobile)->exists();
        
        $customerData = [
            'name' => null,
            'status' => 'Active',
            'joined' => now()->toDateString(),
        ];

        if ($isNewUser && $request->filled('referral_code')) {
            // Check in professionals first, then customers
            $referrer = \App\Models\Professional::where('referral_code', $request->referral_code)->first();
            $referrerType = 'professional';
            
            if (!$referrer) {
                $referrer = Customer::where('referral_code', $request->referral_code)->first();
                $referrerType = 'client';
            }

            if ($referrer) {
                $customerData['referred_by'] = $referrer->id;
                // We'll create the referral record AFTER the customer is created to get the ID
            }
        }

        $customer = Customer::firstOrCreate(
            ['mobile' => $request->mobile],
            $customerData
        );

        $coinsAwarded = 0;
        if ($isNewUser) {
            $rewardService = app(RewardService::class);
            
            // 1. Award Signup Reward (Automatic)
            $coinsAwarded += $rewardService->awardSignupReward($customer, 'customer');

            // 2. Award Referral Rewards (Automatic)
            if (isset($customerData['referred_by'])) {
                // We re-fetch referrer to ensure we have the model instance
                $referrer = \App\Models\Professional::find($customerData['referred_by']);
                $referrerType = 'professional';
                
                if (!$referrer) {
                    $referrer = Customer::find($customerData['referred_by']);
                    $referrerType = 'customer';
                }

                if ($referrer) {
                    $coinsAwarded += $rewardService->awardReferralRewards(
                        $customer, 
                        'customer', 
                        $referrer, 
                        $referrerType, 
                        $request->referral_code
                    );
                }

            }
        }

        if ($customer->status === 'Blocked') {
            return $this->error('Your account has been blocked. Please contact support.', 403);
        }

        try {
            if (!$token = $this->guard()->login($customer)) {
                return $this->error('Unauthorized', 401);
            }

            return $this->success([
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => $this->guard()->factory()->getTTL() * 60,
                'user'         => $customer,
                'is_new'       => $isNewUser,
                'coins_awarded' => $coinsAwarded,
            ], 'Verification successful.');
        } catch (JWTException $e) {
            return $this->error('Could not create token.', 500);
        }
    }

    // ─── 3. AUTHENTICATED CUSTOMER PROFILE ─────────────────────────

    /**
     * GET /api/flutter/auth/me
     */
    public function me(): JsonResponse
    {
        $customer = $this->guard()->user();

        return $this->success(
            new CustomerResource($customer),
            'Customer profile retrieved.'
        );
    }

    // ─── 4. REFRESH TOKEN ──────────────────────────────────────────

    /**
     * POST /api/flutter/auth/refresh
     */
    public function refresh(): JsonResponse
    {
        try {
            $token = $this->guard()->refresh();
        } catch (JWTException $e) {
            return $this->error('Could not refresh token. Please login again.', 401);
        }

        return $this->tokenResponse($token, 'Token refreshed.');
    }

    // ─── 5. LOGOUT ─────────────────────────────────────────────────

    /**
     * POST /api/flutter/auth/logout
     */
    public function logout(): JsonResponse
    {
        try {
            $this->guard()->logout();
        } catch (JWTException $e) {
            return $this->error('Could not invalidate token.', 500);
        }

        return $this->success(null, 'Successfully logged out.');
    }
}
