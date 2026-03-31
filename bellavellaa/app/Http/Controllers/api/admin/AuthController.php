<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends BaseController
{
    // ─── Guard helper ──────────────────────────────────────────────

    protected function guard()
    {
        return Auth::guard('admin-api');
    }

    // ─── 1. LOGIN (email + password) ───────────────────────────────

    /**
     * POST /api/admin/auth/login
     *
     * Body: { "email": "admin@bellavella.com", "password": "secret" }
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Find admin by email
        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return $this->error('Invalid email or password.', 401);
        }

        if (!$admin->is_active) {
            return $this->error('Your account has been deactivated. Please contact support.', 403);
        }

        try {
            $token = $this->guard()->login($admin);
        } catch (JWTException $e) {
            return $this->error('Could not create token.', 500);
        }

        // Update last login info
        $admin->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        return $this->tokenResponse($token, 'Login successful.');
    }

    // ─── 2. AUTHENTICATED ADMIN PROFILE ────────────────────────────

    /**
     * GET /api/admin/auth/me
     */
    public function me(): JsonResponse
    {
        $admin = $this->guard()->user();

        return $this->success([
            'id'            => $admin->id,
            'name'          => $admin->name,
            'email'         => $admin->email,
            'phone'         => $admin->phone,
            'avatar'        => $admin->avatar,
            'role'          => $admin->role,
            'is_active'     => $admin->is_active,
            'last_login_at' => $admin->last_login_at?->toIso8601String(),
        ], 'Admin profile retrieved.');
    }

    // ─── 3. REFRESH TOKEN ──────────────────────────────────────────

    /**
     * POST /api/admin/auth/refresh
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

    // ─── 4. LOGOUT ─────────────────────────────────────────────────

    /**
     * POST /api/admin/auth/logout
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
