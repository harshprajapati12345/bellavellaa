<?php

namespace App\Http\Requests\Professional\Job;

use Illuminate\Foundation\Http\FormRequest;

class PaymentConfirmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_id' => 'required|string',
            'amount' => 'required|numeric',
        ];
    }
}
