<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class SearchReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('view-Report');
    }

    public function rules(): array
    {
        return [
            'query'               => 'nullable|string|max:255',
            'purchase_request_id' => 'nullable|exists:purchase_requests,id',
            'committee_id'        => 'nullable|exists:committees,id',
            'status'              => 'nullable|in:draft,submitted,approved,rejected',
            'created_by'          => 'nullable|exists:users,id',
            'order_by'            => 'nullable|in:report_title,created_at,status',
            'direction'           => 'nullable|in:asc,desc',
        ];
    }
}