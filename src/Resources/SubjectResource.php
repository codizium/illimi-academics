<?php

namespace Illimi\Academics\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'is_compulsory' => $this->is_compulsory,
            'credit_units' => $this->credit_units,
            'teacher_ids' => $this->whenLoaded('teachers', fn () => $this->teachers->pluck('id')->values()->all()),
            'teacher_names' => $this->whenLoaded('teachers', fn () => $this->teachers->map(fn ($teacher) => $teacher->full_name ?? trim(($teacher->first_name ?? '').' '.($teacher->last_name ?? '')))->values()->all()),
            'class_ids' => $this->whenLoaded('classes', fn () => $this->classes->pluck('id')->values()->all()),
            'class_names' => $this->whenLoaded('classes', fn () => $this->classes->pluck('name')->values()->all()),
            'current_syllabus_id' => $this->whenLoaded('currentSyllabus', fn () => $this->currentSyllabus?->id),
            'current_syllabus_title' => $this->whenLoaded('currentSyllabus', fn () => $this->currentSyllabus?->title),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
