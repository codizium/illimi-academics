<?php

namespace Illimi\Academics\Requests;

use Illimi\Academics\Models\Question;
use Illimi\Academics\Requests\Concerns\ValidatesAcademicRelationships;
use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
{
    use ValidatesAcademicRelationships;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_bank_id' => ['sometimes', 'uuid', $this->scopedExists('illimi_question_banks')],
            'subject_id' => ['sometimes', 'uuid', $this->scopedExists('illimi_subjects')],
            'type' => ['sometimes', 'string', 'in:mcq,true_false,short_answer,code_snippet'],
            'content' => ['sometimes', 'string'],
            'options' => ['nullable', 'array'],
            'correct_answer' => ['sometimes'],
            'explanation' => ['nullable', 'string'],
            'difficulty' => ['nullable', 'string', 'in:easy,medium,hard'],
            'marks' => ['nullable', 'numeric', 'min:0'],
            'tags' => ['nullable', 'array'],
            'curriculum_ref' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $question = Question::query()
                ->when($this->organizationId(), fn ($query, $organizationId) => $query->where('organization_id', $organizationId))
                ->find($this->route('question'));

            $this->validateQuestionBankSubjectPair(
                $validator,
                $this->input('question_bank_id', $question?->question_bank_id),
                $this->input('subject_id', $question?->subject_id)
            );
        });
    }
}
