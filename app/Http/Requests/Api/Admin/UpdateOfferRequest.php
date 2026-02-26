<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiFormRequest;

class UpdateOfferRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $offer = $this->route('offer');
        $id = is_object($offer) ? $offer->id : $offer;

        return [
            'name'           => ['sometimes', 'required', 'string', 'max:255'],
            'code'           => ['sometimes', 'required', 'string', 'max:50', 'unique:offers,code,' . $id],
            'discount_type'  => ['sometimes', 'required', 'in:fixed,percentage'],
            'discount_value' => ['sometimes', 'required', 'numeric', 'min:0'],
            'valid_from'     => ['nullable', 'date'],
            'valid_until'    => ['nullable', 'date', 'after_or_equal:valid_from'],
            'status'         => ['nullable', 'in:Active,Inactive'],
        ];
    }
}
