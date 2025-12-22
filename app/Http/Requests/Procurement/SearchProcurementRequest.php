<?php

namespace App\Http\Requests\Procurement;

use Illuminate\Foundation\Http\FormRequest;

class SearchProcurementRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('view-Procurement');
    }

    public function rules(): array
    {
        return [
            'query'              => 'nullable|string|max:255',
            'estimate_id'        => 'nullable|exists:estimates,id',
            'purchase_request_id'=> 'nullable|exists:purchase_requests,id',
            'status'             => 'nullable|in:pending,approved,rejected,completed',
            'supplier_name'      => 'nullable|string|max:255',
            'min_total_cost'     => 'nullable|numeric|min:0',
            'max_total_cost'     => 'nullable|numeric|min:0|gte:min_total_cost',
            'order_by'           => 'nullable|in:procurement_number,supplier_name,total_cost,created_at',
            'direction'          => 'nullable|in:asc,desc',
        ];
    }
}