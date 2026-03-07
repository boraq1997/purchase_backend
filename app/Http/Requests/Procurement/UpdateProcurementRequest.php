<?php

namespace App\Http\Requests\Procurement;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProcurementRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('edit-Procurement');
    }

    public function rules(): array
    {
        $procurementId = $this->route('procurement');

        return [
            'purchase_request_id'          => 'sometimes|exists:purchase_requests,id',
            'reference_no'                 => "sometimes|nullable|string|max:100|unique:procurements,reference_no,{$procurementId}",
            'purchase_date'                => 'sometimes|nullable|date',
            'status'                       => 'sometimes|nullable|in:in_progress,completed,cancelled',
            'notes'                        => 'nullable|string',

            'items'                        => 'sometimes|array|min:1',
            'items.*.estimate_id'          => 'required_with:items|exists:estimates,id',
            'items.*.estimate_item_id'     => 'required_with:items|exists:estimate_items,id',
            'items.*.item_name'            => 'required_with:items|string|max:255',
            'items.*.unit_id'              => 'nullable|exists:units,id',
            'items.*.quantity'             => 'required_with:items|numeric|min:1',
            'items.*.unit_price'           => 'nullable|numeric|min:0',
            'items.*.purchase_price'       => 'required_with:items|numeric|min:0',
            'items.*.estimate_price'       => 'required_with:items|numeric|min:0',
            'items.*.notes'                => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'purchase_request_id.exists'        => 'طلب الشراء غير موجود',
            'reference_no.unique'               => 'رقم المرجع مستخدم مسبقاً',
            'items.min'                         => 'يجب إضافة مادة واحدة على الأقل',
            'items.*.estimate_id.required_with' => 'يرجى تحديد عرض السعر',
            'items.*.estimate_id.exists'        => 'عرض السعر غير موجود',
            'items.*.estimate_item_id.required_with' => 'يرجى تحديد المادة من عرض السعر',
            'items.*.estimate_item_id.exists'   => 'المادة غير موجودة في عرض السعر',
            'items.*.item_name.required_with'   => 'اسم المادة مطلوب',
            'items.*.quantity.required_with'    => 'الكمية مطلوبة',
            'items.*.purchase_price.required_with' => 'سعر الشراء مطلوب',
            'items.*.estimate_price.required_with' => 'سعر عرض السعر مطلوب',
        ];
    }
}