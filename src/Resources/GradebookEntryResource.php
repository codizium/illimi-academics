<?php

namespace Illimi\Academics\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradebookEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'student_name' => $this->whenLoaded('student', fn () => $this->student->full_name ?? trim(($this->student->first_name ?? '').' '.($this->student->last_name ?? ''))),
            'student' => $this->whenLoaded('student', function () {
                return [
                    'id' => $this->student->id,
                    'full_name' => $this->student->full_name ?? trim(($this->student->first_name ?? '').' '.($this->student->last_name ?? '')),
                    'email' => $this->student->email ?? null,
                ];
            }),
            'subject_id' => $this->subject_id,
            'subject_name' => $this->whenLoaded('subject', fn () => $this->subject->name),
            'subject' => $this->whenLoaded('subject', function () {
                return [
                    'id' => $this->subject->id,
                    'name' => $this->subject->name,
                    'code' => $this->subject->code,
                ];
            }),
            'class_id' => $this->class_id,
            'class_name' => $this->whenLoaded('academicClass', fn () => $this->academicClass->name),
            'academic_session' => $this->academic_session,
            'term' => $this->term,
            'component' => $this->component?->value ?? $this->component,
            'score' => (float) $this->score,
            'max_score' => (float) $this->max_score,
            'weight' => (float) $this->weight,
            'entered_by' => $this->entered_by,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
