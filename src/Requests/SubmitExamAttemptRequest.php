<?php

namespace Illimi\Academics\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitExamAttemptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'uuid', 'exists:illimi_students,id'],
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.question_id' => ['required', 'uuid', 'exists:illimi_questions,id'],
            'answers.*.answer' => ['required'],
        ];
    }
}
