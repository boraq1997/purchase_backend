<?php

namespace App\Http\Requests\PurchaseImage;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequestImagesRequest extends FormRequest
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
            'purchase_request_id' => [
                'required',
                'exists:purchase_requests,id'
            ],
            'images' => [
                'required',
                'array',
                'min:1'
            ],
            'images.*' => [
                'file',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
        ];
    }

    public function messages(): array {
        return [
            'purchase_request_id.required' => 'يجب تحديد طلب الشراء.',
            'purchase_request_id.exists'   => 'طلب الشراء غير موجود.',

            'images.required' => 'يجب رفع صورة واحدة على الأقل.',
            'images.array'    => 'صيغة الصور غير صحيحة.',
            'images.min'      => 'يجب رفع صورة واحدة على الأقل.',

            'images.*.mimes' => 'يسمح فقط بصيغ jpg, jpeg, png, webp.',
            'images.*.max'   => 'حجم الصورة يجب ألا يتجاوز 2حجم الصورة يجب ألا يتجاوز MB.',
        ];
    }
}
