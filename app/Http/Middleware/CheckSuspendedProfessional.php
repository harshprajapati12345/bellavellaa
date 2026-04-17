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

        if ($user && $user->status !== 'active') {
            return response()->json([
                'success' => false,
                'status' => 'suspended',
                'message' => 'Your account has been suspended due to excessive rejections. It will reset tomorrow.',
            ], 403);
        }

        return $next($request);
    }
}
