<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiFormRequest;

class StoreBannerRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'media_file'     => ['required', 'image', 'max:5120'], // 5MB max for images
            'linked_section' => ['nullable', 'string'],
            'target_page'    => ['nullable', 'string'],
            'order'          => ['nullable', 'integer'],
            'status'         => ['nullable', 'in:Active,Inactive'],
        ];
    }
}
