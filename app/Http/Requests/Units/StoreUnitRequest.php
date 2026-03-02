<?php

namespace App\Http\Requests\Units;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create-Department');
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255|unique:units,name',
            'description' => 'nullable|string|max:1000',
            'code'        => 'nullable|string|max:50|unique:units,code',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الوحدة مطلوب',
            'name.unique'   => 'اسم الوحدة مستخدم مسبقاً',
            'name.max'      => 'اسم الوحدة طويل جداً',

            'code.unique'   => 'كود الوحدة مستخدم مسبقاً',
            'code.max'      => 'كود الوحدة طويل جداً',
        ];
    }
}