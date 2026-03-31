<?php

namespace App\Http\Requests\Professional\Job;

use Illuminate\Foundation\Http\FormRequest;

class CompleteJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Add rules if needed
        ];
    }
}
