<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Base Form Request for all API endpoints.
 *
 * Overrides the default redirect-on-failure behaviour so
 * validation errors always return a JSON envelope.
 */
abstract class ApiFormRequest extends FormRequest
{
    /**
     * API requests are always JSON — never redirect.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'data'    => null,
                'errors'  => $validator->errors(),
            ], 422)
        );
    }

    /**
     * By default, all API requests are authorized.
     * Override in child classes for specific auth checks.
     */
    public function authorize(): bool
    {
        return true;
    }
}
