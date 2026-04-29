<?php

namespace Illimi\Academics\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchemeOfWorkRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'syllabus_id' => ['sometimes', 'uuid', 'exists:illimi_syllabi,id'],
            'academic_year_id' => ['sometimes', 'uuid', 'exists:illimi_academic_years,id'],
            'academic_term_id' => ['sometimes', 'uuid', 'exists:illimi_academic_terms,id'],
            'teacher_id' => ['sometimes', 'uuid', 'exists:illimi_staff,id'],
            'class_id' => ['nullable', 'uuid', 'exists:illimi_classes,id'],
            'week_number' => ['sometimes', 'integer', 'min:1'],
            'topic' => ['sometimes', 'string', 'max:255'],
            'sub_topics' => ['nullable', 'array'],
            'learning_objectives' => ['nullable', 'array'],
            'teaching_aids' => ['nullable', 'array'],
            'assessment_methods' => ['nullable', 'array'],
            'remarks' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:draft,review,approved'],
        ];
    }
}
