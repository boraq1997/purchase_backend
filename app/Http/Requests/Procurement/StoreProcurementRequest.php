<?php

namespace App\Http\Requests\Procurement;

use Illuminate\Foundation\Http\FormRequest;

class StoreProcurementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && 
        auth()->user()->can('create-Procurement');
    }

    public function rules(): array
    {
        return [
            // ─── بيانات عملية الشراء ───
            'purchase_request_id'         => 'required|exists:purchase_requests,id',
            'reference_no'                => 'nullable|string|max:100|unique:procurements,reference_no',
            'purchase_date'               => 'nullable|date',
            'status'                      => 'nullable|in:in_progress,completed,cancelled',
            'notes'                       => 'nullable|string',

            // ─── عروض الأسعار المختارة (اختياري) ───
            'selected_estimate_ids'       => 'nullable|array',
            'selected_estimate_ids.*'     => 'exists:estimates,id',

            // ─── المواد ───
            'items'                       => 'required|array|min:1',
            'items.*.request_item_id'     => 'nullable|exists:request_items,id',
            'items.*.estimate_id'         => 'nullable|exists:estimates,id',
            'items.*.estimate_item_id'    => 'nullable|exists:estimate_items,id',
            'items.*.item_name'           => 'required|string|max:255',
            'items.*.unit_id'             => 'nullable|exists:units,id',
            'items.*.quantity'            => 'required|numeric|min:0.01',
            'items.*.unit_price'          => 'nullable|numeric|min:0',
            'items.*.purchase_price'      => 'required|numeric|min:0',
            'items.*.estimate_price'      => 'nullable|numeric|min:0',
            'items.*.notes'               => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'purchase_request_id.required'    => 'يرجى اختيار طلب الشراء',
            'purchase_request_id.exists'      => 'طلب الشراء غير موجود',
            'selected_estimate_ids.*.exists'  => 'أحد عروض الأسعار المختارة غير موجود',
            'items.required'                  => 'يجب إضافة مادة واحدة على الأقل',
            'items.min'                       => 'يجب إضافة مادة واحدة على الأقل',
            'items.*.item_name.required'      => 'اسم المادة مطلوب',
            'items.*.quantity.required'       => 'الكمية مطلوبة',
            'items.*.quantity.min'            => 'الكمية يجب أن تكون أكبر من صفر',
            'items.*.purchase_price.required' => 'سعر الشراء الفعلي مطلوب',
            'items.*.purchase_price.min'      => 'سعر الشراء يجب أن يكون صفراً أو أكثر',
        ];
    }
}