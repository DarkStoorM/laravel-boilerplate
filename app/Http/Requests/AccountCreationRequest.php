<?php

namespace App\Http\Requests;

use App\Libs\Constants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AccountCreationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "email" => "required|email|confirmed|unique:users,email",
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

    public function prepareForValidation(): Request
    {
        return $this->merge([
            'email' => strtolower($this->email),
        ]);
    }
}
