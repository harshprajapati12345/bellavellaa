<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:categories,slug|max:255',
            'status' => 'nullable|in:Active,Inactive',
            'featured' => 'nullable|boolean',
            'color' => 'nullable|string|max:7',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
        ];
    }
}
