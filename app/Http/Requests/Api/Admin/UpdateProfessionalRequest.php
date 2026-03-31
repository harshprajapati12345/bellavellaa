<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfessionalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:professionals,email,' . $this->professional->id,
            'phone' => 'sometimes|required|string|unique:professionals,phone,' . $this->professional->id,
            'category' => 'nullable|string',
            'city' => 'nullable|string',
            'bio' => 'nullable|string',
            'experience' => 'nullable|string',
            'rating' => 'nullable|numeric|min:0|max:5',
            'status' => 'nullable|in:Active,Suspended,Blocked',
            'verification' => 'nullable|in:Pending,Verified,Rejected',
            'avatar' => 'nullable|string',
        ];
    }
}
