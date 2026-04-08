<?php

namespace Illimi\Academics\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionBankResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'subject_id' => $this->subject_id,
            'class_id' => $this->class_id,
            'subject_name' => $this->whenLoaded('subject', fn () => $this->subject?->name),
            'class_name' => $this->whenLoaded('academicClass', fn () => $this->academicClass?->name),
            'questions_count' => $this->whenCounted('questions'),
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
