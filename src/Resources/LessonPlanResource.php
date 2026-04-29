<?php

namespace Illimi\Academics\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'scheme_of_work_id' => $this->scheme_of_work_id,
            'scheme_of_work' => new SchemeOfWorkResource($this->whenLoaded('schemeOfWork')),
            'teacher_id' => $this->teacher_id,
            'teacher' => $this->whenLoaded('teacher'),
            'date' => $this->date?->format('Y-m-d'),
            'duration_minutes' => $this->duration_minutes,
            'topic' => $this->topic,
            'learning_outcomes' => $this->learning_outcomes ?? [],
            'introduction' => $this->introduction,
            'presentation_steps' => $this->presentation_steps ?? [],
            'evaluation' => $this->evaluation,
            'conclusion' => $this->conclusion,
            'teaching_aids' => $this->teaching_aids ?? [],
            'status' => $this->status,
            'attachments' => $this->whenLoaded('attachments'),
            'is_completed' => $this->is_completed,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
