<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiFormRequest;

class StoreVideoRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'media_file'     => ['required', 'file', 'mimes:mp4,mov,avi,wmv', 'max:51200'], // 50MB max for videos
            'thumbnail'      => ['required', 'image', 'max:2048'],
            'linked_section' => ['nullable', 'string'],
            'target_page'    => ['nullable', 'string'],
            'order'          => ['nullable', 'integer'],
            'status'         => ['nullable', 'in:Active,Inactive'],
        ];
    }
}
