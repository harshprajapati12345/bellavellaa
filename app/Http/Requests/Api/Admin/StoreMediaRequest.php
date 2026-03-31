<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiFormRequest;

class StoreMediaRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'type'           => ['required', 'in:Banner,Video'],
            'media_file'     => ['required', 'file', 'max:20480'], // 20MB max
            'thumbnail'      => ['nullable', 'image', 'max:2048'],
            'linked_section' => ['nullable', 'string'],
            'target_page'    => ['nullable', 'string'],
            'order'          => ['nullable', 'integer'],
            'status'         => ['nullable', 'in:Active,Inactive'],
        ];
    }
}
