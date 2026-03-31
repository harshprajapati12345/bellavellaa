<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'title'       => 'nullable|string|max:255',
            'subtitle'    => 'nullable|string|max:500',
            'banner_type' => 'required|in:slider,promo',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'      => 'required|in:Active,Inactive',
            'sort_order'  => 'nullable|integer|min:0',
        ];
    }
}
