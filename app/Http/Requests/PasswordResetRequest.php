<?php

namespace App\Http\Requests;

use App\Libs\Constants;
use App\Rules\Throttle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class PasswordResetRequest extends FormRequest
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
                    'throttle-password-reset',
                    Constants::PASSWORD_RESET_MAX_REQUEST_ATTEMPTS,
                    Constants::PASSWORD_RESET_THROTTLE_TIMEOUT,
                    trans('password_reset.validation.throttled')
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => trans('password_reset.validation.email-required'),
            'email.email' => trans('password_reset.validation.email-invalid'),
        ];
    }

    public function prepareForValidation(): Request
    {
        return $this->merge([
            'email' => strtolower($this->email),
        ]);
    }
}
