<?php

namespace App\Http\Requests\NeedsAssessment;

use Illuminate\Foundation\Http\FormRequest;

class SearchNeedsAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('view-NeedsAssessment');
    }

    public function rules(): array
    {
        return [
            'query'               => 'nullable|string|max:255',
            'purchase_request_id' => 'nullable|exists:purchase_requests,id',
            'request_item_id'     => 'nullable|exists:request_items,id',
            'urgency_level'       => 'nullable|in:low,medium,high,critical',
            'assessed_by'         => 'nullable|exists:users,id',
            'order_by'            => 'nullable|in:urgency_level,created_at',
            'direction'           => 'nullable|in:asc,desc',
        ];
    }
}