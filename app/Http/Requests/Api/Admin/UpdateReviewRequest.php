<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiFormRequest;

class UpdateReviewRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'       => ['sometimes', 'required', 'in:Pending,Approved,Rejected'],
            'points_given' => ['sometimes', 'required', 'integer', 'min:0'],
            'is_featured'  => ['sometimes', 'required', 'boolean'],
        ];
    }
}
