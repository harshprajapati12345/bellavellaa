<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuspendedProfessional
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('professional-api');

        if ($user && $user->status === 'suspended') {
            return response()->json([
                'success' => false,
                'is_suspended' => true,
                'status' => 'suspended',
                'message' => 'Your account has been suspended.',
            ], 403);
        }

        return $next($request);
    }
}
