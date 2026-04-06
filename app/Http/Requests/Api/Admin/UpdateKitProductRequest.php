<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateKitProductRequest extends ApiFormRequest
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
        $kitProduct = $this->route('kit_product');

        return [
            'sku'             => [
                'sometimes', 
                'string', 
                Rule::unique('kit_products', 'sku')->ignore($kitProduct->id)
            ],
            'name'            => ['sometimes', 'string', 'max:255'],
            'description'     => ['nullable', 'string'],
            'brand'           => ['nullable', 'string', 'max:255'],
            'category_id'     => ['sometimes', 'exists:categories,id'],
            'unit'            => ['nullable', 'string', 'max:50'],
            'price'           => ['sometimes', 'numeric', 'min:0'],
            'min_stock'       => ['sometimes', 'integer', 'min:0'],
            'total_stock'     => ['sometimes', 'integer', 'min:0'],
            'expiry_date'     => ['nullable', 'date'],
            'status'          => ['sometimes', 'in:Active,Inactive'],
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
