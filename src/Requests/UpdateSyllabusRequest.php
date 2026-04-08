<?php

namespace Illimi\Academics\Requests;

use Illimi\Academics\Requests\Concerns\ValidatesAcademicRelationships;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSyllabusRequest extends FormRequest
{
    use ValidatesAcademicRelationships;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_id' => ['sometimes', 'uuid', $this->scopedExists('illimi_subjects')],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'objectives' => ['nullable', 'string'],
            'topics' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['file', 'max:10240'],
            'remove_attachment_ids' => ['nullable', 'array'],
            'remove_attachment_ids.*' => ['string', 'exists:core_attachments,id'],
        ];
    }
}
