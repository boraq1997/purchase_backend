<?php

namespace App\Http\Requests\Estimate;

use Illuminate\Foundation\Http\FormRequest;

class StoreEstimateWithItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create-Estimate');
    }

    public function rules(): array
    {
        return [
            // بيانات الهيدر الخاص بعرض السعر
            'vendor_id'      => 'required|exists:vendors,id',
            'estimate_date'  => 'nullable|date',
            'status'         => 'nullable|in:pending,accepted,rejected',
            'notes'          => 'nullable|string',

            // قائمة المواد المرتبطة بهذا العرض
            'items'                         => 'required|array|min:1',
            'items.*.request_item_id'       => 'required|exists:request_items,id',
            'items.*.quantity'              => 'nullable|integer|min:1',
            'items.*.unit_price'            => 'required|numeric|min:0',
            'items.*.notes'                 => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'vendor_id.required'              => 'اسم الجهة المقدّمة للعرض مطلوب.',
            'items.required'                    => 'يجب اختيار مادة واحدة على الأقل.',
            'items.*.request_item_id.required'  => 'رقم المادة مطلوب.',
            'items.*.request_item_id.exists'    => 'المادة المحددة غير موجودة.',
            'items.*.unit_price.required'       => 'سعر الوحدة مطلوب لكل مادة.',
            'items.*.unit_price.numeric'        => 'سعر الوحدة يجب أن يكون رقمياً.',
        ];
    }
}