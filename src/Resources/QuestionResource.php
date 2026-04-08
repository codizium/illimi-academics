<?php

namespace Illimi\Academics\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question_bank_id' => $this->question_bank_id,
            'question_bank_name' => $this->whenLoaded('questionBank', fn () => $this->questionBank?->name),
            'subject_id' => $this->subject_id,
            'subject_name' => $this->whenLoaded('subject', fn () => $this->subject?->name),
            'type' => $this->type?->value ?? $this->type,
            'content' => $this->content,
            'options' => $this->options,
            'correct_answer' => $this->correct_answer,
            'explanation' => $this->explanation,
            'difficulty' => $this->difficulty,
            'marks' => $this->marks !== null ? (float) $this->marks : null,
            'tags' => $this->tags,
            'curriculum_ref' => $this->curriculum_ref,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
