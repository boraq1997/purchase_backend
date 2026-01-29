<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUserPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
        
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "old_password" => "required|string",
            "new_password" => "required|string|min:6|different:old_password|confirmed",
        ];
    }

    public function messages() {
        return [
            'old_password.required' => "كلمة المرور الحالية مطلوبة",
            'new_password.required' => "كلمة المرور الجديده مطلوبة",
            'new_password.min' => "كلمة المرور الجديده يجب ان تكون 6 احرف على الاقل",
            'new_password.confirmed' => "تأكيد كلمة المرور غير متطابق",
            "new_password.different" => "كلمة المرور الجديده يجب ان تختلف عن الحالية"
        ];
    }
}
