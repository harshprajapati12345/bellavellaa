<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiFormRequest;

class StorePackageRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            // Basic Info
            'name'                => ['required', 'string', 'max:255'],
            'category'            => ['nullable', 'string', 'max:255'],

            // Services (JSON or array)
            'services'            => ['nullable', 'array'],
            'services.*'          => ['string'],

            // Pricing
            'price'               => ['required', 'numeric', 'min:0'],
            'discount'            => ['nullable', 'numeric', 'min:0'],
            'duration_in_minutes' => ['nullable', 'integer', 'min:0'],

            // Status & Feature
            'status'              => ['nullable', 'in:Active,Inactive'],
            'featured'            => ['nullable', 'boolean'],

            // Description
            'description'         => ['nullable', 'string'],
            'desc_title'          => ['nullable', 'string', 'max:255'],

            // Aftercare
            'aftercare_content'   => ['nullable', 'string'],

            // Images
            'image'               => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'desc_image'          => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'aftercare_image'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],

        ];
    }
}