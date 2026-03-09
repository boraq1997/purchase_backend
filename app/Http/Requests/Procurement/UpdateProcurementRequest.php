<?php

namespace App\Http\Requests\Procurement;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProcurementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && 
        auth()->user()->can('edit-Procurement');
    }

    public function rules(): array
    {
        $procurementId = $this->route('procurement')?->id ?? $this->route('procurement');

        return [
            // ─── بيانات عملية الشراء ───
            'purchase_request_id'         => 'sometimes|exists:purchase_requests,id',
            'reference_no'                => "sometimes|nullable|string|max:100|unique:procurements,reference_no,{$procurementId}",
            'purchase_date'               => 'sometimes|nullable|date',
            'status'                      => 'sometimes|nullable|in:in_progress,completed,cancelled',
            'notes'                       => 'nullable|string',

            // ─── عروض الأسعار المختارة (اختياري) ───
            'selected_estimate_ids'       => 'nullable|array',
            'selected_estimate_ids.*'     => 'exists:estimates,id',

            // ─── المواد ───
            'items'                       => 'sometimes|array|min:1',
            'items.*.request_item_id'     => 'nullable|exists:request_items,id',
            'items.*.estimate_id'         => 'nullable|exists:estimates,id',
            'items.*.estimate_item_id'    => 'nullable|exists:estimate_items,id',
            'items.*.item_name'           => 'required_with:items|string|max:255',
            'items.*.unit_id'             => 'nullable|exists:units,id',
            'items.*.quantity'            => 'required_with:items|numeric|min:0.01',
            'items.*.unit_price'          => 'nullable|numeric|min:0',
            'items.*.purchase_price'      => 'required_with:items|numeric|min:0',
            'items.*.estimate_price'      => 'nullable|numeric|min:0',
            'items.*.notes'               => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'purchase_request_id.exists'          => 'طلب الشراء غير موجود',
            'reference_no.unique'                 => 'رقم المرجع مستخدم مسبقاً',
            'selected_estimate_ids.*.exists'      => 'أحد عروض الأسعار غير موجود',
            'items.min'                           => 'يجب إضافة مادة واحدة على الأقل',
            'items.*.item_name.required_with'     => 'اسم المادة مطلوب',
            'items.*.quantity.required_with'      => 'الكمية مطلوبة',
            'items.*.quantity.min'                => 'الكمية يجب أن تكون أكبر من صفر',
            'items.*.purchase_price.required_with' => 'سعر الشراء الفعلي مطلوب',
        ];
    }
}