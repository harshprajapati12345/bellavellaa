<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'jwt.auth'  => \App\Http\Middleware\JwtAuthenticate::class,
            'jwt.admin' => \App\Http\Middleware\JwtAdminAuthenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // ── Global API exception handler ───────────────────────────
        // Every /api/* request gets clean JSON, never HTML.

        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Endpoint not found.',
                    'data'    => null,
                    'errors'  => null,
                ], 404);
            }
        });

        $exceptions->renderable(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Method not allowed.',
                    'data'    => null,
                    'errors'  => null,
                ], 405);
            }
        });

        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                    'data'    => null,
                    'errors'  => null,
                ], 401);
            }
        });

        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Unauthorized.',
                    'data'    => null,
                    'errors'  => null,
                ], 401);
            }
        });

        $exceptions->renderable(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'data'    => null,
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        $exceptions->renderable(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') && !app()->hasDebugModeEnabled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Internal server error.',
                    'data'    => null,
                    'errors'  => null,
                ], 500);
            }
        });
    })
    ->create();
