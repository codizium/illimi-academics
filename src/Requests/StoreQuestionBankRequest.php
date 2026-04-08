<?php

namespace Illimi\Academics\Requests;

use Illimi\Academics\Requests\Concerns\ValidatesAcademicRelationships;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionBankRequest extends FormRequest
{
    use ValidatesAcademicRelationships;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'subject_id' => ['nullable', 'uuid', $this->scopedExists('illimi_subjects')],
            'class_id' => ['nullable', 'uuid', $this->scopedExists('illimi_classes')],
            'created_by' => ['nullable', 'uuid'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateSubjectClassPair($validator, $this->input('subject_id'), $this->input('class_id'));
        });
    }
}
