<?php

namespace App\Http\Requests\Procurement;

use Illuminate\Foundation\Http\FormRequest;

class StoreProcurementRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create-Procurement');
    }

    public function rules(): array
    {
        return [
            'purchase_request_id'          => 'required|exists:purchase_requests,id',
            'reference_no'                 => 'nullable|string|max:100|unique:procurements,reference_no',
            'purchase_date'                => 'nullable|date',
            'status'                       => 'nullable|in:in_progress,completed,cancelled',
            'notes'                        => 'nullable|string',

            'items'                        => 'required|array|min:1',
            'items.*.estimate_id'          => 'required|exists:estimates,id',
            'items.*.estimate_item_id'     => 'required|exists:estimate_items,id',
            'items.*.item_name'            => 'required|string|max:255',
            'items.*.unit_id'              => 'nullable|exists:units,id',
            'items.*.quantity'             => 'required|numeric|min:1',
            'items.*.unit_price'           => 'nullable|numeric|min:0',
            'items.*.purchase_price'       => 'required|numeric|min:0',
            'items.*.estimate_price'       => 'required|numeric|min:0',
            'items.*.notes'                => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'purchase_request_id.required'      => 'يرجى اختيار طلب الشراء',
            'purchase_request_id.exists'        => 'طلب الشراء غير موجود',
            'items.required'                    => 'يجب إضافة مادة واحدة على الأقل',
            'items.min'                         => 'يجب إضافة مادة واحدة على الأقل',
            'items.*.estimate_id.required'      => 'يرجى تحديد عرض السعر',
            'items.*.estimate_id.exists'        => 'عرض السعر غير موجود',
            'items.*.estimate_item_id.required' => 'يرجى تحديد المادة من عرض السعر',
            'items.*.estimate_item_id.exists'   => 'المادة غير موجودة في عرض السعر',
            'items.*.item_name.required'        => 'اسم المادة مطلوب',
            'items.*.quantity.required'         => 'الكمية مطلوبة',
            'items.*.quantity.min'              => 'الكمية يجب أن تكون أكبر من صفر',
            'items.*.purchase_price.required'   => 'سعر الشراء مطلوب',
            'items.*.purchase_price.min'        => 'سعر الشراء يجب أن يكون أكبر من أو يساوي صفر',
            'items.*.estimate_price.required'   => 'سعر عرض السعر مطلوب',
        ];
    }
}