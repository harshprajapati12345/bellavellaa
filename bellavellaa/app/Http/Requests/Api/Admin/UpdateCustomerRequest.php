<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiFormRequest;

class UpdateCustomerRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('customer') ? $this->route('customer')->id : null;

        return [
            'name'     => ['sometimes', 'required', 'string', 'max:255'],
            'mobile'   => ['sometimes', 'required', 'string', 'unique:customers,mobile,' . $id],
            'avatar'   => ['nullable', 'string'],
            'city'     => ['nullable', 'string', 'max:255'],
            'zip'      => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string'],
            'status'   => ['nullable', 'in:Active,Blocked'],
            'bookings' => ['nullable', 'integer', 'min:0'],
            'joined'   => ['nullable', 'date'],
        ];
    }
}
