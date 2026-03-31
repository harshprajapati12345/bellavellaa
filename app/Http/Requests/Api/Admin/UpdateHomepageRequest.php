<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiFormRequest;

class UpdateHomepageRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'section'       => ['sometimes', 'string', 'unique:homepage_contents,section,' . $this->route('homepage')],
            'title'         => ['sometimes', 'string', 'max:255'],
            'content'       => ['sometimes', 'array'],
            'section_image' => ['sometimes', 'image', 'max:5120'],
            'status'        => ['sometimes', 'in:Active,Inactive'],
            'sort_order'    => ['sometimes', 'integer'],
        ];
    }
}
