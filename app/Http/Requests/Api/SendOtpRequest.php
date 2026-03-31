<?php

namespace App\Http\Requests\Api;

class SendOtpRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'mobile' => ['required', 'string', 'digits:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.required' => 'Mobile number is required.',
            'mobile.digits'   => 'Mobile number must be exactly 10 digits.',
        ];
    }
}
