<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiFormRequest;

class StoreServiceRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:255'],
            'category'            => ['nullable', 'string', 'max:255'],
            'price'               => ['required', 'numeric', 'min:0'],
            'duration_in_minutes' => ['nullable', 'integer', 'min:0'],
            'status'              => ['nullable', 'in:Active,Inactive'],
            'featured'            => ['nullable', 'boolean'],
            'image'               => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'description'         => ['nullable', 'string'],
        ];
    }
}
