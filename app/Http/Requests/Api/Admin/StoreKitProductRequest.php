<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiFormRequest;

class StoreKitProductRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku'             => ['required', 'string', 'unique:kit_products,sku'],
            'name'            => ['required', 'string', 'max:255'],
            'price'           => ['required', 'numeric', 'min:0'],
            'min_stock'       => ['required', 'integer', 'min:0'],
            'total_stock'     => ['required', 'integer', 'min:0'],
            'available_stock' => ['nullable', 'integer', 'min:0'],
            'status'          => ['nullable', 'in:Active,Inactive'],
        ];
    }
}
