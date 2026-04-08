<?php

namespace Illimi\Academics\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ResultCollection extends ResourceCollection
{
    public $collects = ResultResource::class;

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->collection->count(),
                'published' => $this->collection->filter(fn ($item) => ($item->status?->value ?? $item->status) === 'published')->count(),
                'under_review' => $this->collection->filter(fn ($item) => ($item->status?->value ?? $item->status) === 'under_review')->count(),
                'draft' => $this->collection->filter(fn ($item) => ($item->status?->value ?? $item->status) === 'draft')->count(),
            ],
        ];
    }
}
