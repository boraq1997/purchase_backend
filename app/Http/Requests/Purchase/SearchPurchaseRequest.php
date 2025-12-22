<?php

namespace App\Http\Requests\Purchases;

use Illuminate\Foundation\Http\FormRequest;

class SearchPurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('view-PurchaseRequest');
    }

    public function rules(): array
    {
        return [
            'query'          => 'nullable|string|max:255',
            'department_id'  => 'nullable|exists:departments,id',
            'user_id'        => 'nullable|exists:users,id',
            'status'         => 'nullable|in:pending,approved,rejected,completed',
            'priority'       => 'nullable|in:low,medium,high',
            'from_date'      => 'nullable|date',
            'to_date'        => 'nullable|date|after_or_equal:from_date',
            'order_by'       => 'nullable|in:title,created_at,status,priority',
            'direction'      => 'nullable|in:asc,desc',
        ];
    }
}