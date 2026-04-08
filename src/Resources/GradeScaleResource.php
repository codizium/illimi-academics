<?php

namespace Illimi\Academics\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeScaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'min_score' => $this->min_score !== null ? (float) $this->min_score : null,
            'max_score' => $this->max_score !== null ? (float) $this->max_score : null,
            'description' => $this->description,
            'is_default' => (bool) $this->is_default,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
