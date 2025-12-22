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
            'estimate_id'       => 'required|exists:estimates,id',
            'purchase_request_id' => 'required|exists:purchase_requests,id',
            'procurement_number' => 'required|string|max:100|unique:procurements,procurement_number',
            'supplier_name'     => 'required|string|max:255',
            'total_cost'        => 'nullable|numeric|min:0',
            'currency'          => 'nullable|string|max:10',
            'status'            => 'nullable|in:pending,approved,rejected,completed',
            'notes'             => 'nullable|string|max:500',
            'items'             => 'nullable|array',
            'items.*.estimate_item_id' => 'required_with:items|exists:estimate_items,id',
            'items.*.quantity'  => 'required_with:items|integer|min:1',
            'items.*.unit_price'=> 'required_with:items|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'estimate_id.required'        => 'estimate id is required',
            'estimate_id.exists'          => 'estimate not found',
            'purchase_request_id.required'=> 'purchase request id is required',
            'purchase_request_id.exists'  => 'purchase request not found',
            'procurement_number.required' => 'procurement number is required',
            'procurement_number.unique'   => 'procurement number already exists',
            'supplier_name.required'      => 'supplier name is required',
            'items.array'                 => 'items must be an array',
            'items.*.estimate_item_id.required_with' => 'estimate item id is required',
            'items.*.estimate_item_id.exists' => 'estimate item not found',
            'items.*.quantity.required_with' => 'item quantity is required',
            'items.*.unit_price.required_with' => 'item unit price is required',
        ];
    }
}