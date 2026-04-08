<?php

namespace Illimi\Academics\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SyllabusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject_id' => $this->subject_id,
            'subject' => $this->whenLoaded('subject', function () {
                return [
                    'id' => $this->subject->id,
                    'name' => $this->subject->name,
                    'code' => $this->subject->code,
                ];
            }),
            'subject_name' => $this->whenLoaded('subject', fn() => $this->subject->name),
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'objectives' => $this->objectives,
            'topics' => $this->topics,
            'is_published' => $this->is_published,
            'attachments_count' => $this->attachments_count ?? $this->whenLoaded('attachments', fn () => $this->attachments->count()),
            'documents' => $this->whenLoaded('attachments', fn () => $this->attachments->map(fn ($attachment) => [
                'id' => $attachment->id,
                'label' => $attachment->label,
                'file_type' => $attachment->file_type,
                'file_url' => $attachment->file_url,
            ])->values()->all()),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
