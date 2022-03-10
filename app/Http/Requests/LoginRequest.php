<?php

namespace App\Http\Requests;

use App\Libs\Constants;
use App\Rules\Throttle;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                new Throttle(
                    'throttle-login',
                    Constants::LOGIN_MAXIMUM_ATTEMPTS,
                    Constants::LOGIN_THROTTLE_TIMEOUT,
                    trans('login.validation.throttled')
                ),
            ],
            'password' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => trans('login.validation.email-required'),
            'email.email' => trans('login.validation.email-invalid'),
            'password.required' => trans('login.validation.password-required'),
        ];
    }
}
