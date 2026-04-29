<?php

namespace Illimi\Academics\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchemeOfWorkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'syllabus_id' => $this->syllabus_id,
            'syllabus' => new SyllabusResource($this->whenLoaded('syllabus')),
            'academic_year_id' => $this->academic_year_id,
            'academic_year' => $this->whenLoaded('academicYear'),
            'academic_term_id' => $this->academic_term_id,
            'academic_term' => $this->whenLoaded('academicTerm'),
            'teacher_id' => $this->teacher_id,
            'teacher' => $this->whenLoaded('teacher'),
            'class_id' => $this->class_id,
            'classroom' => $this->whenLoaded('classroom'),
            'week_number' => $this->week_number,
            'topic' => $this->topic,
            'sub_topics' => $this->sub_topics ?? [],
            'learning_objectives' => $this->learning_objectives ?? [],
            'teaching_aids' => $this->teaching_aids ?? [],
            'assessment_methods' => $this->assessment_methods ?? [],
            'remarks' => $this->remarks,
            'status' => $this->status,
            'attachments' => $this->whenLoaded('attachments'),
            'lesson_plans_count' => $this->whenCounted('lessonPlans'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
