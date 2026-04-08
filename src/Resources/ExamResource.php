<?php

namespace Illimi\Academics\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subject_id' => $this->subject_id,
            'subject_name' => $this->whenLoaded('subject', fn () => $this->subject?->name),
            'class_id' => $this->class_id,
            'class_name' => $this->whenLoaded('academicClass', fn () => $this->academicClass?->name),
            'academic_session' => $this->academic_session,
            'term' => $this->term,
            'duration_minutes' => $this->duration_minutes,
            'total_marks' => $this->total_marks !== null ? (float) $this->total_marks : null,
            'pass_mark' => $this->pass_mark !== null ? (float) $this->pass_mark : null,
            'negative_marking' => (bool) $this->negative_marking,
            'negative_mark_value' => $this->negative_mark_value !== null ? (float) $this->negative_mark_value : null,
            'randomise_questions' => (bool) $this->randomise_questions,
            'randomise_options' => (bool) $this->randomise_options,
            'allow_review' => (bool) $this->allow_review,
            'status' => $this->status?->value ?? $this->status,
            'starts_at' => $this->starts_at?->toIso8601String(),
            'ends_at' => $this->ends_at?->toIso8601String(),
            'created_by' => $this->created_by,
            'proctoring_options' => $this->proctoring_options,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
