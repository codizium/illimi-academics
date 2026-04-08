<?php

namespace Illimi\Academics\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradeAppealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'result_id' => ['required', 'uuid', 'exists:illimi_results,id'],
            'student_id' => ['nullable', 'uuid', 'exists:illimi_students,id'],
            'reason' => ['required', 'string'],
        ];
    }
}
