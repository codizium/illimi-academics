<?php

namespace Illimi\Academics\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeAppealResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'result_id' => $this->result_id,
            'result_label' => $this->whenLoaded('result', function () {
                $studentName = $this->result?->student?->full_name ?? trim(($this->result?->student?->first_name ?? '').' '.($this->result?->student?->last_name ?? ''));
                $className = $this->result?->academicClass?->name;

                return trim(implode(' - ', array_filter([
                    $studentName,
                    $className,
                    $this->result?->academic_session,
                    $this->result?->term,
                ])));
            }),
            'student_id' => $this->student_id,
            'student_name' => $this->whenLoaded('student', fn () => $this->student->full_name ?? trim(($this->student->first_name ?? '').' '.($this->student->last_name ?? ''))),
            'reason' => $this->reason,
            'status' => $this->status?->value ?? $this->status,
            'submitted_at' => $this->submitted_at?->toIso8601String(),
            'resolved_at' => $this->resolved_at?->toIso8601String(),
            'resolution' => $this->resolution,
            'resolved_by' => $this->resolved_by,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
