<?php

namespace Illimi\Academics\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subjectId = $this->route('subject');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => ['sometimes', 'string', 'max:50', Rule::unique('illimi_subjects', 'code')->ignore($subjectId)],
            'description' => ['nullable', 'string'],
            'is_compulsory' => ['nullable', 'boolean'],
            'credit_units' => ['nullable', 'integer', 'min:0'],
            'teacher_ids' => ['nullable', 'array'],
            'teacher_ids.*' => ['uuid', 'exists:illimi_staff,id'],
            'class_ids' => ['nullable', 'array'],
            'class_ids.*' => ['uuid', 'exists:illimi_classes,id'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Subject name is required',
            'code.required' => 'Subject code is required',
            'teacher_ids.exists' => 'One or more teachers do not exist',
            'class_ids.exists' => 'One or more classes do not exist',
        ];
    }
}
