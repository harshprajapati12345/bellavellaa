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
            $data['otp'] = $otp->otp; // For Flutter auto-fill
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
                return $this->error('Invalid or expired OTP.', 403);
            }
        }

        $professional = Professional::where('phone', $request->mobile)->first();

        if (!$professional) {
            return $this->success([
                'is_new_user' => true,
                'mobile' => $request->mobile,
            ], 'OTP verified. Please complete signup.');
        }

        if ($professional->status === 'Suspended') {
            return $this->error('Your account has been suspended. Please contact support.', 403);
        }

        try {
            $token = $this->guard()->login($professional);
        }
        catch (JWTException $e) {
            return $this->error('Could not create token.', 500);
        }

        // Login reward decommissioned

        return $this->success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
            'user' => [
                'id' => $professional->id,
                'name' => $professional->name,
                'verification' => $professional->verification,
                'status' => $professional->status,
            ],
            'is_new_user' => false,
        ], 'Login successful.');
    }

    /**
     * POST /api/professional/register
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'mobile' => 'required|string|digits:10',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'category' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'dob' => 'nullable|string',
            'gender' => 'nullable|string',
            'experience' => 'nullable|string',
            'languages' => 'nullable|string',
            'address' => 'nullable|string',
            'pincode' => 'nullable|string|digits:6',
            'state' => 'nullable|string',
            'aadhar' => 'nullable|string|digits:12',
            'pan' => 'nullable|string|size:10',
            'aadhar_front' => 'nullable|image|max:2048',
            'aadhar_back' => 'nullable|image|max:2048',
            'pan_photo' => 'nullable|image|max:2048',
            'certificate' => 'nullable|image|max:2048',
            'light_bill' => 'nullable|image|max:2048',
            'selfie' => 'nullable|image|max:2048',
            'referral_code' => 'nullable|string|exists:professionals,referral_code',
            // Banking Details
            'account_holder_name' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
            'upi_id' => 'nullable|string|max:100',
            'bank_proof' => 'nullable|image|max:2048',
        ]);

        $alreadyVerified = Otp::where('mobile', $request->mobile)
            ->where('purpose', 'professional_login')
            ->where('verified', true)
            ->where('updated_at', '>=', now()->subMinutes(15))
            ->latest()->first();

        if (!$alreadyVerified) {
            return $this->error('Please verify OTP before signing up.', 403);
        }

        $professional = Professional::where('phone', $request->mobile)->first();

        if ($professional) {
            return $this->error('This mobile number is already registered.', 400);
        }

        $data = [
            'phone' => $request->mobile,
            'name' => $request->name,
            'email' => $request->email,
            'category' => $request->category,
            'city' => $request->city,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'experience' => $request->experience,
            'languages' => $request->languages ? explode(', ', $request->languages) : [],
            'service_area' => $request->address,
            'pincode' => $request->pincode,
            'state' => $request->state,
            'aadhaar' => $request->aadhar,
            'pan' => $request->pan,
            'account_holder_name' => $request->account_holder_name,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'ifsc_code' => $request->ifsc_code,
            'upi_id' => $request->upi_id,
            'status' => 'Active',
            'verification' => 'Pending',
            'joined' => now()->toDateString(),
        ];

        if ($request->hasFile('bank_proof')) {
            $path = $request->file('bank_proof')->store('professionals/bank_proofs', 'public');
            $data['bank_proof'] = '/storage/' . $path;
        }

        // Handle Referral
        if ($request->filled('referral_code')) {
            $referrer = Professional::where('referral_code', $request->referral_code)->first();
            if ($referrer) {
                $data['referred_by'] = $referrer->id;
            }
        }

        // Handle File Uploads
        if ($request->hasFile('aadhar_front')) {
            $path = $request->file('aadhar_front')->store('documents/aadhaar', 'public');
            $data['aadhaar_front'] = '/storage/' . $path;
        }
        if ($request->hasFile('aadhar_back')) {
            $path = $request->file('aadhar_back')->store('documents/aadhaar', 'public');
            $data['aadhaar_back'] = '/storage/' . $path;
        }
        if ($request->hasFile('pan_photo')) {
            $path = $request->file('pan_photo')->store('documents/pan', 'public');
            $data['pan_img'] = '/storage/' . $path;
        }
        if ($request->hasFile('certificate')) {
            $path = $request->file('certificate')->store('documents/certificates', 'public');
            $data['certificate_img'] = '/storage/' . $path;
        }
        if ($request->hasFile('light_bill')) {
            $path = $request->file('light_bill')->store('documents/light-bills', 'public');
            $data['light_bill'] = '/storage/' . $path;
        }
        if ($request->hasFile('selfie')) {
            $path = $request->file('selfie')->store('avatars', 'public');
            $data['selfie'] = '/storage/' . $path;
            $data['avatar'] = '/storage/' . $path;
        }

        // Auto-set docs flag if all uploaded
        if (isset($data['aadhaar_front']) && isset($data['aadhaar_back']) && isset($data['pan_img'])) {
            $data['docs'] = true;
        }

        $professional = Professional::create($data);

        // Referral reward system decommissioned (handled on first job completion if active)

        try {
            $token = $this->guard()->login($professional);
        }
        catch (JWTException $e) {
            return $this->error('Could not create token.', 500);
        }

        return $this->success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
            'user' => [
                'id' => $professional->id,
                'name' => $professional->name,
                'verification' => $professional->verification,
                'status' => $professional->status,
            ],
        ], 'Registration successful.');
    }

    /**
     * POST /api/professional/login
     */
    public function login(Request $request): JsonResponse
    {
        // Placeholder for login logic (e.g. check version, start session)
        return $this->success(null, 'Login initiated.');
    }

    /**
     * GET /api/professional/verification-status
     */
    public function verificationStatus(): JsonResponse
    {
        $professional = $this->guard()->user();

        $map = [
            'aadhaar_front',
            'aadhaar_back',
            'pan_card' => 'pan_img',
            'light_bill',
            'bank_proof',
        ];

        $documents = [];

        foreach ($map as $key => $column) {
            $apiKey = is_numeric($key) ? $column : $key;
            $file = $professional->$column;

            $documents[$apiKey] = [
                'url' => \App\Support\MediaPathNormalizer::url($file),
                'status' => $this->getDocStatus($professional, $column),
                'type' => $file ? pathinfo($file, PATHINFO_EXTENSION) : null,
            ];
        }

        return $this->success([
            'verification' => $professional->verification,
            'status' => $professional->status,
            'docs' => (bool)$professional->docs,
            'documents' => $documents,
        ], 'Verification status retrieved.');
    }

    private function getDocStatus($user, $column)
    {
        $statusField = $column . '_status';

        if (!empty($user->$statusField)) {
            return $user->$statusField; // approved / rejected / pending
        }

        return $user->$column ? 'pending' : 'not_uploaded';
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
        }
        catch (JWTException $e) {
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
        }
        catch (JWTException $e) {
            return $this->error('Could not invalidate token.', 500);
        }

        return $this->success(null, 'Successfully logged out.');
    }
}
