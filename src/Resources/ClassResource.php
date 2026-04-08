<?php

namespace Illimi\Academics\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'level' => $this->level,
            'section_id' => $this->section_id,
            'section' => $this->whenLoaded('section', function () {
                return [
                    'id' => $this->section->id,
                    'name' => $this->section->name,
                ];
            }),
            'section_name' => $this->whenLoaded('section', fn() => $this->section->name),
            'classroom_id' => $this->classroom_id,
            'classroom' => $this->whenLoaded('classroom', function () {
                return [
                    'id' => $this->classroom->id,
                    'name' => $this->classroom->name,
                    'location' => $this->classroom->location,
                ];
            }),
            'classroom_name' => $this->whenLoaded('classroom', fn() => $this->classroom->name),
            'class_teacher_id' => $this->class_teacher_id,
            'class_teacher' => $this->whenLoaded('classTeacher', function () {
                return [
                    'id' => $this->classTeacher->id,
                    'first_name' => $this->classTeacher->first_name ?? null,
                    'last_name' => $this->classTeacher->last_name ?? null,
                    'email' => $this->classTeacher->email ?? null,
                ];
            }),
            'class_teacher_name' => $this->whenLoaded('classTeacher', fn() => trim(($this->classTeacher->first_name ?? '') . ' ' . ($this->classTeacher->last_name ?? ''))),
            'capacity' => $this->capacity,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
