<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiFormRequest;

class StoreOfferRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255'],
            'code'           => ['required', 'string', 'max:50', 'unique:offers,code'],
            'discount_type'  => ['required', 'in:fixed,percentage'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'max_discount_paise' => ['nullable', 'integer', 'min:0'],
            'min_order_paise'    => ['nullable', 'integer', 'min:0'],
            'usage_limit'        => ['nullable', 'integer', 'min:1'],
            'per_user_limit'     => ['nullable', 'integer', 'min:1'],
            'target_type'        => ['nullable', 'string', 'max:50'],
            'target_id'          => ['nullable', 'integer', 'min:1'],
            'valid_from'     => ['nullable', 'date'],
            'valid_until'    => ['nullable', 'date', 'after_or_equal:valid_from'],
            'status'         => ['nullable', 'in:Active,Inactive'],
        ];
    }
}
