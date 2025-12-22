<?php

namespace App\Http\Requests\Vendors;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('edit-Vendors');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $vendor = $this->route('vendor');
        $vendorId = $vendor ? $vendor->id : null;

        return [
            'name'       => 'sometimes|required|string|max:255',
            'phone1'     => "sometimes|required|string|max:20|unique:vendors,phone1,{$vendorId}",
            'phone2'     => "nullable|string|max:20|unique:vendors,phone2,{$vendorId}",
            'email'      => "sometimes|required|email|max:255|unique:vendors,email,{$vendorId}",
            'address'    => 'sometimes|required|string|max:500',
        ];
    }

    public function messages(){
        return [
            'name.required'     => 'Vendor name is required',
            'phone1.required'   => 'Phone1 is required',
            'phone1.unique'     => 'Phone1 already exists',
            'phone2.unique'     => 'Phone2 already exists',
            'email.required'    => 'Email is required',
            'email.email'       => 'Invalid email format',
            'address.required'  => 'Address is required',
        ];
    }
}
