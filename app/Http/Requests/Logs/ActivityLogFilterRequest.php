<?php

namespace App\Http\Requests\Logs;

use Illuminate\Foundation\Http\FormRequest;

class ActivityLogFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        // يمكنك إضافة شرط للتأكد من صلاحية المستخدم إذا أحببت
        return true;
    }

    public function rules(): array
    {
        return [
            'actor_type' => 'nullable|string',
            'actor_id' => 'nullable|integer',
            'actor_name' => 'nullable|string',
            'actor_role' => 'nullable|string',

            'subject_type' => 'nullable|string',
            'subject_id' => 'nullable|integer',
            'subject_identifier' => 'nullable|string',

            'action' => 'nullable|string',
            'action_label' => 'nullable|string',

            'status' => 'nullable|string|in:success,failed',
            'severity' => 'nullable|string|in:info,warning,critical',

            'module' => 'nullable|string',
            'route' => 'nullable|string',
            'method' => 'nullable|string',

            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'q' => 'nullable|string',

            'metadata' => 'nullable|array',
            'metadata.*' => 'string',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}