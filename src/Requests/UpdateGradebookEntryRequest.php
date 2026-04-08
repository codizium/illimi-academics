<?php

namespace Illimi\Academics\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradebookEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['sometimes', 'uuid', 'exists:illimi_students,id'],
            'subject_id' => ['sometimes', 'uuid', 'exists:illimi_subjects,id'],
            'class_id' => ['sometimes', 'uuid', 'exists:illimi_classes,id'],
            'academic_session' => ['sometimes', 'string', 'max:50'],
            'term' => ['sometimes', 'string', 'max:50'],
            'component' => ['sometimes', 'string', 'in:exam,continuous_assessment,practical,project'],
            'score' => ['sometimes', 'numeric', 'min:0'],
            'max_score' => ['sometimes', 'numeric', 'min:0'],
            'weight' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'entered_by' => ['nullable', 'uuid'],
        ];
    }
}
