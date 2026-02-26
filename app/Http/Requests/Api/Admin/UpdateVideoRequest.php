<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiFormRequest;

class UpdateVideoRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'          => ['sometimes', 'string', 'max:255'],
            'media_file'     => ['sometimes', 'file', 'mimes:mp4,mov,avi,wmv', 'max:51200'],
            'thumbnail'      => ['sometimes', 'image', 'max:2048'],
            'linked_section' => ['nullable', 'string'],
            'target_page'    => ['nullable', 'string'],
            'order'          => ['sometimes', 'integer'],
            'status'         => ['sometimes', 'in:Active,Inactive'],
        ];
    }
}
