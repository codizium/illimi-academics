<?php

namespace Illimi\Academics\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'level' => ['sometimes', 'string', 'max:100'],
            'section_id' => ['nullable', 'uuid', 'exists:illimi_sections,id'],
            'classroom_id' => ['nullable', 'uuid', 'exists:illimi_classrooms,id'],
            'class_teacher_id' => ['nullable', 'uuid', 'exists:illimi_staff,id'],
            'capacity' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
