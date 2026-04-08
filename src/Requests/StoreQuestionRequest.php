<?php

namespace Illimi\Academics\Requests;

use Illimi\Academics\Requests\Concerns\ValidatesAcademicRelationships;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    use ValidatesAcademicRelationships;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_bank_id' => ['required', 'uuid', $this->scopedExists('illimi_question_banks')],
            'subject_id' => ['required', 'uuid', $this->scopedExists('illimi_subjects')],
            'type' => ['required', 'string', 'in:mcq,true_false,short_answer,code_snippet'],
            'content' => ['required', 'string'],
            'options' => ['nullable', 'array'],
            'correct_answer' => ['required'],
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
            $this->validateQuestionBankSubjectPair($validator, $this->input('question_bank_id'), $this->input('subject_id'));
        });
    }
}
