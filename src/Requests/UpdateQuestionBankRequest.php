<?php

namespace Illimi\Academics\Requests;

use Illimi\Academics\Models\QuestionBank;
use Illimi\Academics\Requests\Concerns\ValidatesAcademicRelationships;
use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionBankRequest extends FormRequest
{
    use ValidatesAcademicRelationships;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'subject_id' => ['nullable', 'uuid', $this->scopedExists('illimi_subjects')],
            'class_id' => ['nullable', 'uuid', $this->scopedExists('illimi_classes')],
            'created_by' => ['nullable', 'uuid'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $questionBank = QuestionBank::query()
                ->when($this->organizationId(), fn ($query, $organizationId) => $query->where('organization_id', $organizationId))
                ->find($this->route('question_bank'));

            $this->validateSubjectClassPair(
                $validator,
                $this->input('subject_id', $questionBank?->subject_id),
                $this->input('class_id', $questionBank?->class_id)
            );
        });
    }
}
