<?php

namespace Illimi\Academics\Requests;

use Illimi\Academics\Requests\Concerns\ValidatesAcademicRelationships;
use Illuminate\Foundation\Http\FormRequest;

class StoreExamRequest extends FormRequest
{
    use ValidatesAcademicRelationships;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subject_id' => ['required', 'uuid', $this->scopedExists('illimi_subjects')],
            'class_id' => ['required', 'uuid', $this->scopedExists('illimi_classes')],
            'academic_session' => ['required', 'string', 'max:50'],
            'term' => ['required', 'string', 'max:50'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'total_marks' => ['required', 'numeric', 'min:0'],
            'pass_mark' => ['nullable', 'numeric', 'min:0'],
            'negative_marking' => ['nullable', 'boolean'],
            'negative_mark_value' => ['nullable', 'numeric', 'min:0'],
            'randomise_questions' => ['nullable', 'boolean'],
            'randomise_options' => ['nullable', 'boolean'],
            'allow_review' => ['nullable', 'boolean'],
            'status' => ['nullable', 'string', 'in:scheduled,ongoing,completed,cancelled'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'proctoring_options' => ['nullable', 'array'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateSubjectClassPair($validator, $this->input('subject_id'), $this->input('class_id'));
        });
    }
}
