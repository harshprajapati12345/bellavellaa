<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfessionalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:professionals,email',
            'phone' => 'required|string|unique:professionals,phone',
            'category' => 'nullable|string',
            'city' => 'nullable|string',
            'bio' => 'nullable|string',
            'experience' => 'nullable|string',
            'rating' => 'nullable|numeric|min:0|max:5',
            'status' => 'nullable|in:Active,Suspended,Blocked',
            'avatar' => 'nullable|string',
        ];
    }
}
