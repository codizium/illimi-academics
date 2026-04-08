<?php

namespace Illimi\Academics\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublishResultsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'result_ids' => ['nullable', 'array', 'min:1', 'required_without_all:class_id,academic_session,term'],
            'result_ids.*' => ['required', 'uuid', 'exists:illimi_results,id'],
            'class_id' => ['nullable', 'uuid', 'exists:illimi_classes,id', 'required_without:result_ids'],
            'academic_session' => ['nullable', 'string', 'max:255', 'required_with:class_id', 'required_without:result_ids'],
            'term' => ['nullable', 'string', 'max:255', 'required_with:class_id', 'required_without:result_ids'],
        ];
    }
}
