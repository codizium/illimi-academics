<?php

namespace Illimi\Academics\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradebookEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'uuid', 'exists:illimi_students,id'],
            'subject_id' => ['required', 'uuid', 'exists:illimi_subjects,id'],
            'class_id' => ['required', 'uuid', 'exists:illimi_classes,id'],
            'academic_session' => ['required', 'string', 'max:50'],
            'term' => ['required', 'string', 'max:50'],
            'component' => ['required', 'string', 'in:exam,continuous_assessment,practical,project'],
            'score' => ['required', 'numeric', 'min:0'],
            'max_score' => ['required', 'numeric', 'min:0'],
            'weight' => ['required', 'numeric', 'min:0', 'max:100'],
            'entered_by' => ['nullable', 'uuid'],
        ];
    }
}
