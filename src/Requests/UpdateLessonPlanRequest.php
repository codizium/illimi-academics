<?php

namespace Illimi\Academics\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonPlanRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'scheme_of_work_id' => ['sometimes', 'uuid', 'exists:illimi_schemes_of_work,id'],
            'teacher_id' => ['sometimes', 'uuid', 'exists:illimi_staff,id'],
            'date' => ['sometimes', 'date'],
            'duration_minutes' => ['sometimes', 'integer', 'min:1'],
            'topic' => ['sometimes', 'string', 'max:255'],
            'learning_outcomes' => ['nullable', 'array'],
            'introduction' => ['nullable', 'string'],
            'presentation_steps' => ['nullable', 'array'],
            'evaluation' => ['nullable', 'string'],
            'conclusion' => ['nullable', 'string'],
            'teaching_aids' => ['nullable', 'array'],
            'status' => ['nullable', 'string', 'in:draft,ready,completed'],
            'is_completed' => ['nullable', 'boolean'],
        ];
    }
}
