<?php

namespace Illimi\Academics\Services;

use Illimi\Academics\Models\Syllabus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class SyllabusService
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Syllabus::query()
            ->with(['subject'])
            ->with(['attachments'])
            ->withCount('attachments');

        if (! empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (array_key_exists('is_published', $filters) && $filters['is_published'] !== null && $filters['is_published'] !== '') {
            $query->where('is_published', (bool) $filters['is_published']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): Syllabus
    {
        return DB::transaction(function () use ($data) {
            $documents = $this->extractDocuments($data);

            $syllabus = Syllabus::create(Arr::except($data, ['documents', 'remove_attachment_ids']));

            $this->attachDocuments($syllabus, $documents);

            return $this->findById($syllabus->id) ?? $syllabus->fresh();
        });
    }

    public function update(string $id, array $data): ?Syllabus
    {
        $syllabus = Syllabus::find($id);

        if (! $syllabus) {
            return null;
        }

        return DB::transaction(function () use ($syllabus, $data) {
            $documents = $this->extractDocuments($data);
            $removeAttachmentIds = collect($data['remove_attachment_ids'] ?? [])
                ->filter()
                ->values()
                ->all();

            $syllabus->update(Arr::except($data, ['documents', 'remove_attachment_ids']));

            foreach ($removeAttachmentIds as $attachmentId) {
                if ($syllabus->attachments()->whereKey($attachmentId)->exists()) {
                    $syllabus->deleteAttachment($attachmentId, true);
                }
            }

            $this->attachDocuments($syllabus, $documents);

            return $this->findById($syllabus->id) ?? $syllabus->fresh();
        });
    }

    public function findById(string $id): ?Syllabus
    {
        return Syllabus::query()
            ->with(['subject', 'attachments'])
            ->withCount('attachments')
            ->find($id);
    }

    public function delete(string $id): bool
    {
        $syllabus = Syllabus::find($id);

        if (! $syllabus) {
            return false;
        }

        foreach ($syllabus->attachments()->pluck('id') as $attachmentId) {
            $syllabus->deleteAttachment($attachmentId, true);
        }

        return (bool) $syllabus->delete();
    }

    protected function extractDocuments(array &$data): array
    {
        $documents = Arr::wrap($data['documents'] ?? []);
        unset($data['documents']);

        return array_values(array_filter($documents, fn ($document) => $document instanceof UploadedFile));
    }

    protected function attachDocuments(Syllabus $syllabus, array $documents): void
    {
        foreach ($documents as $document) {
            $syllabus->attach(
                $document,
                $document->getClientOriginalName(),
                'public',
                sprintf('academics/%s/syllabi/%s', $syllabus->organization_id ?? 'shared', $syllabus->id)
            );
        }
    }
}
