<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiFormRequest;

class StoreAssignmentRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_id'      => ['required', 'exists:bookings,id'],
            'professional_id' => ['required', 'exists:professionals,id'],
        ];
    }
}
