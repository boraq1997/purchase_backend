<?php

namespace App\Http\Requests\EstimateImage;

use Illuminate\Foundation\Http\FormRequest;

class StoreEstimateImageRequest extends FormRequest
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
            'estimate_id' => [
                'required',
                'exists:estimates,id',
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
            'estimate_id.required' => 'يجب تحديد عرض السعر.',
            'estimate_id.exists'   => 'عرض السعر غير موجود.',

            'images.required' => 'يجب رفع صورة واحدة على الأقل.',
            'images.array'    => 'صيغة الصور غير صحيحة.',
            'images.min'      => 'يجب رفع صورة واحدة على الأقل.',

            'images.*.mimes' => 'يسمح فقط بصيغ jpg, jpeg, png.',
            'images.*.max'   => 'حجم الصورة يجب ألا يتجاوز 2حجم الصورة يجب ألا يتجاوز MB.',
        ];
    }
}
