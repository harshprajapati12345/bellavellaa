<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiFormRequest;

class StoreHomepageRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'section'       => ['required', 'string', 'unique:homepage_contents,section'],
            'title'         => ['nullable', 'string', 'max:255'],
            'content'       => ['required', 'array'],
            'section_image' => ['nullable', 'image', 'max:5120'],
            'status'        => ['nullable', 'in:Active,Inactive'],
            'sort_order'    => ['nullable', 'integer'],
        ];
    }
}
