<?php

namespace Illimi\Academics\Services;

use Illimi\Academics\Models\Subject;
use Illuminate\Pagination\LengthAwarePaginator;

class SubjectService
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Subject::query()->teacher()->with(['teachers', 'classes', 'currentSyllabus']);

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): Subject
    {
        $subject = Subject::create($this->subjectPayload($data));
        $this->syncRelations($subject, $data);

        return $this->findById($subject->id) ?? $subject->fresh();
    }

    public function update(string $id, array $data): ?Subject
    {
        $subject = Subject::find($id);

        if (! $subject) {
            return null;
        }

        $subject->update($this->subjectPayload($data));
        $this->syncRelations($subject, $data, false);

        return $this->findById($subject->id) ?? $subject->fresh();
    }

    public function findById(string $id): ?Subject
    {
        return Subject::query()->teacher()->with(['teachers', 'classes', 'currentSyllabus'])->find($id);
    }

    public function delete(string $id): bool
    {
        $subject = Subject::find($id);

        if (! $subject) {
            return false;
        }

        $subject->teachers()->detach();
        $subject->classes()->detach();

        return (bool) $subject->delete();
    }

    protected function subjectPayload(array $data): array
    {
        return collect($data)
            ->except(['teacher_ids', 'class_ids'])
            ->toArray();
    }

    protected function syncRelations(Subject $subject, array $data, bool $isCreate = true): void
    {
        if ($isCreate || array_key_exists('teacher_ids', $data)) {
            $subject->teachers()->sync($this->pivotPayload($subject->organization_id, $data['teacher_ids'] ?? []));
        }

        if ($isCreate || array_key_exists('class_ids', $data)) {
            $subject->classes()->sync($this->pivotPayload($subject->organization_id, $data['class_ids'] ?? []));
        }
    }

    protected function pivotPayload(?string $organizationId, array $ids): array
    {
        return collect($ids)
            ->filter()
            ->unique()
            ->mapWithKeys(fn ($id) => [$id => ['organization_id' => $organizationId]])
            ->all();
    }
}
