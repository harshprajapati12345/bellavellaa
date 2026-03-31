<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreHierarchyBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'placement_type' => 'required|in:page_header,promo_banner,popup_banner',
            'media_type' => 'required|in:image,video',
            'media_file' => 'required|file|mimes:jpg,jpeg,png,webp,mp4,mov,webm|max:20480',
            'thumbnail_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'target_type' => 'required|in:category,service_group,service_type,service,variant',
            'target_id' => 'required|integer|min:1',
            'action_link' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:80',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:Active,Inactive',
        ];
    }
}
