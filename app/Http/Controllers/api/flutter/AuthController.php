<?php

namespace App\Http\Controllers\Api\Flutter;

use App\Http\Requests\Api\SendOtpRequest;
use App\Http\Requests\Api\VerifyOtpRequest;
use App\Http\Resources\Api\CustomerResource;
use App\Models\Customer;
use App\Models\Otp;
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
     * Generates a 6-digit OTP, invalidates any previous unsent OTPs
     * for the same mobile, and (in production) dispatches it via SMS.
     */
    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $otp = Otp::generate($request->mobile, 'login', 5);

        // TODO: integrate SMS gateway
        // SmsService::send($request->mobile, "Your BellaVella OTP is {$otp->otp}");

        $data = [
            'expires_in' => 300, // 5 minutes in seconds
        ];

        // Show OTP in non-production environments for testing
        if (!app()->isProduction()) {
            $data['otp_debug'] = $otp->otp;
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
        $customer = Customer::firstOrCreate(
            ['mobile' => $request->mobile],
            [
                'name'   => null,
                'status' => 'Active',
                'joined' => now()->toDateString(),
            ]
        );

        if ($customer->status === 'Blocked') {
            return $this->error('Your account has been blocked. Please contact support.', 403);
        }

        try {
            $token = $this->guard()->login($customer);
        } catch (JWTException $e) {
            return $this->error('Could not create token.', 500);
        }

        return $this->tokenResponse($token, 'Login successful.');
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
