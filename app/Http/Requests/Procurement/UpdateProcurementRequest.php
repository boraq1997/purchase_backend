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
            'estimate_id'         => 'sometimes|required|exists:estimates,id',
            'purchase_request_id' => 'sometimes|required|exists:purchase_requests,id',
            'procurement_number'  => "sometimes|required|string|max:100|unique:procurements,procurement_number,{$procurementId}",
            'supplier_name'       => 'sometimes|required|string|max:255',
            'total_cost'          => 'nullable|numeric|min:0',
            'currency'            => 'nullable|string|max:10',
            'status'              => 'nullable|in:pending,approved,rejected,completed',
            'notes'               => 'nullable|string|max:500',
            'items'               => 'nullable|array',
            'items.*.id'          => 'nullable|exists:procurement_items,id',
            'items.*.estimate_item_id' => 'required_with:items|exists:estimate_items,id',
            'items.*.quantity'    => 'required_with:items|integer|min:1',
            'items.*.unit_price'  => 'required_with:items|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'estimate_id.exists'          => 'estimate not found',
            'purchase_request_id.exists'  => 'purchase request not found',
            'procurement_number.unique'   => 'procurement number already exists',
            'supplier_name.required'      => 'supplier name is required',
            'items.array'                 => 'items must be an array',
            'items.*.id.exists'           => 'invalid procurement item id',
        ];
    }
}