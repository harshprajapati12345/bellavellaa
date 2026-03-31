<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiFormRequest;

class UpdateMediaRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'          => ['sometimes', 'string', 'max:255'],
            'type'           => ['sometimes', 'in:Banner,Video'],
            'media_file'     => ['sometimes', 'file', 'max:20480'],
            'thumbnail'      => ['sometimes', 'image', 'max:2048'],
            'linked_section' => ['nullable', 'string'],
            'target_page'    => ['nullable', 'string'],
            'order'          => ['sometimes', 'integer'],
            'status'         => ['sometimes', 'in:Active,Inactive'],
        ];
    }
}
