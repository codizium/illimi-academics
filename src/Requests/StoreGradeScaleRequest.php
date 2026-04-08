<?php

namespace Illimi\Academics\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradeScaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:illimi_grade_scales,code'],
            'min_score' => ['required', 'numeric', 'min:0'],
            'max_score' => ['required', 'numeric', 'gte:min_score'],
            'description' => ['nullable', 'string'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }
}
