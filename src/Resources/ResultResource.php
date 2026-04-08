<?php

namespace Illimi\Academics\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResultResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'student' => $this->whenLoaded('student', function () {
                return [
                    'id' => $this->student->id,
                    'full_name' => $this->student->full_name ?? trim(($this->student->first_name ?? '').' '.($this->student->last_name ?? '')),
                    'email' => $this->student->email ?? null,
                ];
            }),
            'class_id' => $this->class_id,
            'academic_session' => $this->academic_session,
            'term' => $this->term,
            'total_score' => $this->total_score !== null ? (float) $this->total_score : null,
            'grade' => $this->grade,
            'grade_point' => $this->grade_point !== null ? (float) $this->grade_point : null,
            'remark' => $this->remark,
            'position_in_class' => $this->position_in_class,
            'status' => $this->status?->value ?? $this->status,
            'published_by' => $this->published_by,
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
