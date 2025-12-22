<?php

namespace App\Http\Requests\Estimate;

use Illuminate\Foundation\Http\FormRequest;

class SearchEstimateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('view-Estimate');
    }

    public function rules(): array
    {
        return [
            'query'               => 'nullable|string|max:255',
            'purchase_request_id' => 'nullable|exists:purchase_requests,id',
            'supplier_name'       => 'nullable|string|max:255',
            'status'              => 'nullable|in:pending,approved,rejected',
            'min_total'           => 'nullable|numeric|min:0',
            'max_total'           => 'nullable|numeric|min:0|gte:min_total',
            'order_by'            => 'nullable|in:supplier_name,total_amount,created_at',
            'direction'           => 'nullable|in:asc,desc',
        ];
    }
}