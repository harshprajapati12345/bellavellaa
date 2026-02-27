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

class JwtAuthenticate
{
    /**
     * Handle an incoming API request.
     *
     * Ensures a valid JWT is present and returns clean JSON errors
     * instead of redirecting to a login page.
     */
    public function handle(Request $request, Closure $next, string $guard = null): Response
    {
        try {
            // Use specified guard or fall back to default
            $user = auth($guard)->authenticate();

            if (!$user) {
                return response()->json(['message' => 'User not found.'], 401);
            }
        } catch (TokenExpiredException $e) {

            return response()->json(['message' => 'Token has expired.'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['message' => 'Token is invalid.'], 401);
        } catch (TokenBlacklistedException $e) {
            return response()->json(['message' => 'Token has been blacklisted.'], 401);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token is missing.'], 401);
        }

        return $next($request);
    }
}
