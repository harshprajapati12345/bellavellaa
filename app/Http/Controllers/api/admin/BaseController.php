<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\adminroutes\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Base controller for all Admin API endpoints.
 *
 * Uses the same JSON envelope as Flutter APIs:
 * {
 *   "success": true|false,
 *   "message": "Human-readable message",
 *   "data":    { ... } | null,
 *   "errors":  { "field": ["..."] } | null
 * }
 */
abstract class BaseController extends Controller
{
    /**
     * Return a success response.
     */
    protected function success(mixed $data = null, string $message = 'OK', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'errors'  => null,
        ], $code);
    }

    /**
     * Return an error response.
     */
    protected function error(string $message = 'Something went wrong.', int $code = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => null,
            'errors'  => $errors,
        ], $code);
    }

    /**
     * Return a validation-error response (422).
     */
    protected function validationError(mixed $errors, string $message = 'Validation failed.'): JsonResponse
    {
        return $this->error($message, 422, $errors);
    }

    /**
     * Standard JWT token response.
     */
    protected function tokenResponse(string $token, string $message = 'Authenticated.'): JsonResponse
    {
        $guard = auth('admin-api');

        return $this->success([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $guard->factory()->getTTL() * 60,
        ], $message);
    }
}
