<?php

namespace App\Http\Requests\Api;

class VerifyOtpRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'mobile' => ['required', 'string', 'digits:10'],
            'otp'    => ['required', 'string', 'size:4'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.required' => 'Mobile number is required.',
            'mobile.digits'   => 'Mobile number must be exactly 10 digits.',
            'otp.required'    => 'OTP is required.',
            'otp.size'        => 'OTP must be exactly 4 characters.',
        ];
    }
}


