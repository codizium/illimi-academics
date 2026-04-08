<?php

namespace Illimi\Academics\Requests;

use Illimi\Academics\Models\Exam;
use Illimi\Academics\Requests\Concerns\ValidatesAcademicRelationships;
use Illuminate\Foundation\Http\FormRequest;

class UpdateExamRequest extends FormRequest
{
    use ValidatesAcademicRelationships;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'subject_id' => ['sometimes', 'uuid', $this->scopedExists('illimi_subjects')],
            'class_id' => ['sometimes', 'uuid', $this->scopedExists('illimi_classes')],
            'academic_session' => ['sometimes', 'string', 'max:50'],
            'term' => ['sometimes', 'string', 'max:50'],
            'duration_minutes' => ['sometimes', 'integer', 'min:1'],
            'total_marks' => ['sometimes', 'numeric', 'min:0'],
            'pass_mark' => ['nullable', 'numeric', 'min:0'],
            'negative_marking' => ['nullable', 'boolean'],
            'negative_mark_value' => ['nullable', 'numeric', 'min:0'],
            'randomise_questions' => ['nullable', 'boolean'],
            'randomise_options' => ['nullable', 'boolean'],
            'allow_review' => ['nullable', 'boolean'],
            'status' => ['nullable', 'string', 'in:scheduled,ongoing,completed,cancelled'],
            'starts_at' => ['sometimes', 'date'],
            'ends_at' => ['sometimes', 'date', 'after:starts_at'],
            'proctoring_options' => ['nullable', 'array'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $exam = Exam::query()
                ->when($this->organizationId(), fn ($query, $organizationId) => $query->where('organization_id', $organizationId))
                ->find($this->route('id'));

            $this->validateSubjectClassPair(
                $validator,
                $this->input('subject_id', $exam?->subject_id),
                $this->input('class_id', $exam?->class_id)
            );
        });
    }
}
