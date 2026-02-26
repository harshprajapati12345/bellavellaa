<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;

class JwtAdminAuthenticate
{
    /**
     * Handle an incoming Admin API request.
     *
     * Validates the JWT against the admin-api guard and returns
     * clean JSON errors instead of redirecting.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Set the guard to admin-api so the token resolves against Admins
            $user = Auth::guard('admin-api')->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Admin not found.',
                'data'    => null,
                'errors'  => null,
            ], 401);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token has expired.',
                'data'    => null,
                'errors'  => null,
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token is invalid.',
                'data'    => null,
                'errors'  => null,
            ], 401);
        } catch (TokenBlacklistedException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token has been blacklisted.',
                'data'    => null,
                'errors'  => null,
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token is missing.',
                'data'    => null,
                'errors'  => null,
            ], 401);
        }

        return $next($request);
    }
}
