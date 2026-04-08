<?php

namespace Illimi\Academics\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateTranscriptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_session' => ['required', 'string', 'max:50'],
            'term' => ['nullable', 'string', 'max:50'],
        ];
    }
}
