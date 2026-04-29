<?php

namespace Illimi\Academics\Services;

use Illimi\Academics\Models\SchemeOfWork;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class SchemeOfWorkService
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SchemeOfWork::query()
            ->with(['syllabus.subject', 'academicYear', 'academicTerm', 'teacher', 'classroom', 'attachments'])
            ->withCount('lessonPlans');

        if (! empty($filters['syllabus_id'])) {
            $query->where('syllabus_id', $filters['syllabus_id']);
        }
        if (! empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }
        if (! empty($filters['academic_term_id'])) {
            $query->where('academic_term_id', $filters['academic_term_id']);
        }
        if (! empty($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }

        return $query->orderBy('week_number')->paginate($perPage);
    }

    public function create(array $data): SchemeOfWork
    {
        return DB::transaction(function () use ($data) {
            $documents = $this->extractDocuments($data);

            $scheme = SchemeOfWork::create(Arr::except($data, ['documents']));

            $this->attachDocuments($scheme, $documents);

            return $this->findById($scheme->id) ?? $scheme->fresh();
        });
    }

    public function update(string $id, array $data): ?SchemeOfWork
    {
        $scheme = SchemeOfWork::find($id);

        if (! $scheme) {
            return null;
        }

        return DB::transaction(function () use ($scheme, $data) {
            $documents = $this->extractDocuments($data);
            $removeIds = collect($data['remove_attachment_ids'] ?? [])->filter()->values()->all();

            $scheme->update(Arr::except($data, ['documents', 'remove_attachment_ids']));

            foreach ($removeIds as $attachmentId) {
                if ($scheme->attachments()->whereKey($attachmentId)->exists()) {
                    $scheme->deleteAttachment($attachmentId, true);
                }
            }

            $this->attachDocuments($scheme, $documents);

            return $this->findById($scheme->id) ?? $scheme->fresh();
        });
    }

    public function findById(string $id): ?SchemeOfWork
    {
        return SchemeOfWork::query()
            ->with(['syllabus.subject', 'academicYear', 'academicTerm', 'teacher', 'classroom', 'attachments', 'lessonPlans'])
            ->withCount('lessonPlans')
            ->find($id);
    }

    public function delete(string $id): bool
    {
        $scheme = SchemeOfWork::find($id);

        if (! $scheme) {
            return false;
        }

        foreach ($scheme->attachments()->pluck('id') as $attachmentId) {
            $scheme->deleteAttachment($attachmentId, true);
        }

        return (bool) $scheme->delete();
    }

    protected function extractDocuments(array &$data): array
    {
        $documents = Arr::wrap($data['documents'] ?? []);
        unset($data['documents']);

        return array_values(array_filter($documents, fn ($d) => $d instanceof UploadedFile));
    }

    protected function attachDocuments(SchemeOfWork $scheme, array $documents): void
    {
        foreach ($documents as $document) {
            $scheme->attach(
                $document,
                $document->getClientOriginalName(),
                'public',
                sprintf('academics/%s/schemes/%s', $scheme->organization_id ?? 'shared', $scheme->id)
            );
        }
    }
}
