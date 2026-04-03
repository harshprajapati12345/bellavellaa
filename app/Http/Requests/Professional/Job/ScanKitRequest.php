<?php

namespace App\Http\Requests\Professional\Job;

use Illuminate\Foundation\Http\FormRequest;

class ScanKitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Add rules if needed, e.g., 'kit_id' => 'required'
        ];
    }
}
