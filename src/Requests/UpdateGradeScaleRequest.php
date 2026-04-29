<?php

namespace Illimi\Academics\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGradeScaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $gradeScaleId = $this->route('grade_scale');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => ['sometimes', 'string', 'max:50', Rule::unique('illimi_grade_scales', 'code')->ignore($gradeScaleId)],
            'min_score' => ['sometimes', 'numeric', 'min:0', 'required_with:max_score'],
            'max_score' => ['sometimes', 'numeric', 'gte:min_score', 'required_with:min_score'],
            'description' => ['nullable', 'string'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }
}
