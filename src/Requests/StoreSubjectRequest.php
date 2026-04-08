<?php

namespace Illimi\Academics\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:illimi_subjects,code'],
            'description' => ['nullable', 'string'],
            'is_compulsory' => ['nullable', 'boolean'],
            'credit_units' => ['nullable', 'integer', 'min:0'],
            'teacher_ids' => ['nullable', 'array'],
            'teacher_ids.*' => ['uuid', 'exists:illimi_staff,id'],
            'class_ids' => ['nullable', 'array'],
            'class_ids.*' => ['uuid', 'exists:illimi_classes,id'],
        ];
    }
}
