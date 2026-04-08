<?php

namespace Illimi\Academics\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'uuid', 'exists:illimi_students,id'],
            'ip_address' => ['nullable', 'string', 'max:45'],
            'browser_info' => ['nullable', 'array'],
        ];
    }
}
