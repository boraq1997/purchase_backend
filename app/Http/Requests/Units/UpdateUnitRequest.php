<?php

namespace App\Http\Requests\Units;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('edit-Department');
    }

    public function rules(): array
    {
        $unitId = $this->route('unit') instanceof \App\Models\Unit 
            ? $this->route('unit')->id 
            : $this->route('unit');     

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('units', 'name')->ignore($unitId),
            ],
            'description' => 'nullable|string|max:1000',
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('units', 'code')->ignore($unitId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الوحدة مطلوب',
            'name.unique'   => 'اسم الوحدة مستخدم مسبقاً',
            'code.unique'   => 'كود الوحدة مستخدم مسبقاً',
        ];
    }
}