<?php

namespace App\Http\Requests;

use App\Libs\Constants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class PasswordResetChangeRequest extends FormRequest
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
            "token" => "required",
            "email" => "required|email",
            "password" => [
                "required",
                "confirmed",
                Password::min(Constants::PASSWORD_MIN_LENGTH)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols(),
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "email.required" => trans("password_reset.validation.password-change.email-missing"),
            "email.email" => trans("password_reset.validation.password-change.mail-invalid"),
            "token.required" => trans("password_reset.validation.password-change.token-missing"),
            "password.required" => trans("password_reset.validation.password-change.password-required"),
            "password.confirmed" => trans("password_reset.validation.password-change.password-confirmed"),
            "password.min" => trans("password_reset.validation.password-change.password-min"),
        ];
    }
}
