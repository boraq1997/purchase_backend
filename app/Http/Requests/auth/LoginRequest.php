<?php

namespace App\Http\Requests\auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => 'required|string',
            'password' => 'required|string|min:4',
        ];
    }

    public function messages(): array {
        return [
            'username.required' => 'USERNAME_IS_REQUIRED',
            'password.required' => 'PASSWORD_IS_REQUIRED',
            'password.min'      => 'PASSWORD_MUST_BE_MARE_THAN_4_CHR',
        ];
    }
}
