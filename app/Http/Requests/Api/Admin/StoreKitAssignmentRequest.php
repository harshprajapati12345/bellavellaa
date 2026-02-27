<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiFormRequest;

class StoreKitAssignmentRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'professional_id' => ['required', 'exists:professionals,id'],
            'kit_product_id'  => ['required', 'exists:kit_products,id'],
            'quantity'        => ['required', 'integer', 'min:1'],
        ];
    }
}
