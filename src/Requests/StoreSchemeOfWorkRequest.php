<?php

namespace Illimi\Academics\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchemeOfWorkRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'syllabus_id' => ['required', 'uuid', 'exists:illimi_syllabi,id'],
            'academic_year_id' => ['required', 'uuid', 'exists:illimi_academic_years,id'],
            'academic_term_id' => ['required', 'uuid', 'exists:illimi_academic_terms,id'],
            'teacher_id' => ['nullable', 'uuid', 'exists:illimi_staff,id'],
            'class_id' => ['nullable', 'uuid', 'exists:illimi_classes,id'],
            'week_number' => ['required', 'integer', 'min:1'],
            'topic' => ['required', 'string', 'max:255'],
            'sub_topics' => ['nullable', 'array'],
            'learning_objectives' => ['nullable', 'array'],
            'teaching_aids' => ['nullable', 'array'],
            'assessment_methods' => ['nullable', 'array'],
            'remarks' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:draft,review,approved'],
        ];
    }

    public function messages(): array
    {
        return [
            'syllabus_id.exists' => 'The selected syllabus does not exist.',
            'academic_year_id.exists' => 'The selected academic year does not exist.',
            'academic_term_id.exists' => 'The selected academic term does not exist.',
            'teacher_id.exists' => 'The selected teacher does not exist.',
            'class_id.exists' => 'The selected class does not exist.',
        ];
    }
}
