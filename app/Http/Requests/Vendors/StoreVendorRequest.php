<?php

namespace App\Http\Requests\Vendors;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create-Vendors');
    }

    /**
    * Get the validation rules that apply to the request.
    *
    * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
    */
    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'phone1'        => 'required|string|max:20|unique:vendors,phone1',
            'phone2'        => 'nullable|string|max:20|unique:vendors,phone2',
            'email'         => 'required|email|unique:vendors,email|max:255',
            'address'       => 'required|string|max:500',
        ];
    }

    public function messages(){
        return [
            'name.required'     => 'vendor name is required',
            'phone1.required'   => 'phone1 is required',
            'phone1.unique'     => 'phone1 is exists',
            'phone2.unique'     => 'phone2 is exists',
            'email.required'    => 'email is required',
            'email.email'       => 'invalid email formate',
            'address.required'  => 'address is required',
        ];
    }
}
