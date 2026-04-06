<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiFormRequest;

class StoreKitProductRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     * Converts empty strings to null for nullable fields like expiry_date.
     */
    protected function prepareForValidation()
    {
        if ($this->has('expiry_date') && empty($this->expiry_date)) {
            $this->merge([
                'expiry_date' => null,
            ]);
        }
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

    /**
     * Get the validated data and convert price to paise.
     */
    public function validated($key = null, $default = null): array
    {
        $data = parent::validated($key, $default);

        if (isset($data['price'])) {
            $data['price'] = (int) round($data['price'] * 100);
        }

        return $data;
    }
}
