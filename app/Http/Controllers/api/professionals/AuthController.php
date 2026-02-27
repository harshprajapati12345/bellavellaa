<?php

namespace App\Http\Controllers\Api\Professionals;

use App\Http\Requests\Api\SendOtpRequest;
use App\Http\Requests\Api\VerifyOtpRequest;
use App\Models\Professional;
use App\Models\Otp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends BaseController
{
    protected function guard()
    {
        return Auth::guard('professional-api');
    }

    /**
     * POST /api/professionals/auth/send-otp
     */
    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $otp = Otp::generate($request->mobile, 'professional_login', 5, 4);

        // TODO: integrate SMS gateway

        $data = [
            'expires_in' => 300,
        ];

        if (!app()->isProduction()) {
            $data['otp_debug'] = $otp->otp;
        }

        return $this->success($data, 'OTP sent successfully.');
    }

    /**
     * POST /api/professionals/auth/verify-otp
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $otpRecord = Otp::verify($request->mobile, $request->otp, 'professional_login');

        if (!$otpRecord) {
            $alreadyVerified = Otp::where('mobile', $request->mobile)
                ->where('purpose', 'professional_login')
                ->where('verified', true)
                ->where('updated_at', '>=', now()->subMinutes(15))
                ->latest()->first();

            if (!$alreadyVerified) {
                return $this->error('Invalid or expired OTP.', 401);
            }
        }

        $professional = Professional::where('phone', $request->mobile)->first();

        if (!$professional) {
            return $this->success([
                'is_new_user' => true,
                'mobile'      => $request->mobile,
            ], 'OTP verified. Please complete signup.');
        }

        if ($professional->status === 'Suspended') {
            return $this->error('Your account has been suspended. Please contact support.', 403);
        }

        try {
            $token = $this->guard()->login($professional);
        } catch (JWTException $e) {
            return $this->error('Could not create token.', 500);
        }

        return $this->success([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $this->guard()->factory()->getTTL() * 60,
            'user'         => [
                'id' => $professional->id,
                'name' => $professional->name,
                'verification' => $professional->verification,
                'status' => $professional->status,
            ],
            'is_new_user'  => false,
        ], 'Login successful.');
    }

    /**
     * POST /api/professionals/auth/signup
     */
    public function signup(Request $request): JsonResponse
    {
        $request->validate([
            'mobile'   => 'required|string|digits:10',
            'name'     => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'city'     => 'nullable|string|max:100',
        ]);

        $alreadyVerified = Otp::where('mobile', $request->mobile)
            ->where('purpose', 'professional_login')
            ->where('verified', true)
            ->where('updated_at', '>=', now()->subMinutes(15))
            ->latest()->first();

        if (!$alreadyVerified) {
            return $this->error('Please verify OTP before signing up.', 401);
        }

        $professional = Professional::where('phone', $request->mobile)->first();

        if ($professional) {
            return $this->error('This mobile number is already registered.', 400);
        }

        $professional = Professional::create([
            'phone'        => $request->mobile,
            'name'         => $request->name,
            'category'     => $request->category,
            'city'         => $request->city,
            'status'       => 'Active',
            'verification' => 'Pending',
            'joined'       => now()->toDateString(),
        ]);

        try {
            $token = $this->guard()->login($professional);
        } catch (JWTException $e) {
            return $this->error('Could not create token.', 500);
        }

        return $this->success([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $this->guard()->factory()->getTTL() * 60,
            'user'         => [
                'id' => $professional->id,
                'name' => $professional->name,
                'verification' => $professional->verification,
                'status' => $professional->status,
            ]
        ], 'Registration successful.');
    }

    /**
     * GET /api/professionals/auth/status
     */
    public function status(): JsonResponse
    {
        $professional = $this->guard()->user();

        return $this->success([
            'verification' => $professional->verification,
            'status'       => $professional->status,
            'docs'         => (bool)$professional->docs,
        ], 'Verification status retrieved.');
    }

    /**
     * GET /api/professionals/auth/me
     */
    public function me(): JsonResponse
    {
        $professional = $this->guard()->user();

        return $this->success($professional, 'Professional profile retrieved.');
    }

    /**
     * POST /api/professionals/auth/refresh
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

    /**
     * POST /api/professionals/auth/logout
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
