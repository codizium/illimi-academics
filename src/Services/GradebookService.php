<?php

namespace Illimi\Academics\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illimi\Academics\Exceptions\GradeWeightingException;
use Illimi\Academics\Models\GradebookEntry;
use Illimi\Academics\Models\Subject;
use Illimi\Academics\Support\GradeCalculator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class GradebookService
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = GradebookEntry::query()->teacher()->with(['student', 'subject', 'academicClass']);

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (!empty($filters['academic_session'])) {
            $query->where('academic_session', $filters['academic_session']);
        }

        if (!empty($filters['term'])) {
            $query->where('term', $filters['term']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function store(array $data): GradebookEntry
    {
        $this->assertTeacherCanAccessSubject($data['subject_id']);

        $entry = $this->queryForRecord($data)->first();
        $this->guardWeighting($data, $entry?->id);

        if ($entry) {
            $entry->update($data);

            return $this->findById($entry->id) ?? $entry->fresh();
        }

        $created = GradebookEntry::create($data);

        return $this->findById($created->id) ?? $created->fresh();
    }

    public function findById(string $id): ?GradebookEntry
    {
        return GradebookEntry::query()
            ->teacher()
            ->with(['student', 'subject', 'academicClass'])
            ->find($id);
    }

    public function update(string $id, array $data): ?GradebookEntry
    {
        $entry = GradebookEntry::query()->teacher()->find($id);

        if (! $entry) {
            return null;
        }

        $payload = array_merge($entry->only([
            'student_id',
            'subject_id',
            'class_id',
            'academic_session',
            'term',
            'component',
            'score',
            'max_score',
            'weight',
            'entered_by',
        ]), $data);

        $this->assertTeacherCanAccessSubject($payload['subject_id']);
        $this->guardWeighting($payload, $entry->id);
        $entry->update($data);

        return $this->findById($entry->id);
    }

    public function delete(string $id): bool
    {
        $entry = GradebookEntry::query()->teacher()->find($id);

        if (! $entry) {
            return false;
        }

        return (bool) $entry->delete();
    }

    public function computeWeightedScore(
        string $studentId,
        string $subjectId,
        string $classId,
        string $academicSession,
        string $term
    ): float {
        $entries = GradebookEntry::query()
            ->teacher()
            ->where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->where('class_id', $classId)
            ->where('academic_session', $academicSession)
            ->where('term', $term)
            ->get();

        return GradeCalculator::calculateWeightedScore($entries);
    }

    protected function queryForRecord(array $data): Builder
    {
        return GradebookEntry::query()
            ->teacher()
            ->where('student_id', $data['student_id'])
            ->where('subject_id', $data['subject_id'])
            ->where('class_id', $data['class_id'])
            ->where('academic_session', $data['academic_session'])
            ->where('term', $data['term'])
            ->where('component', $data['component']);
    }

    protected function guardWeighting(array $data, ?string $ignoreId = null): void
    {
        $weightQuery = GradebookEntry::query()
            ->teacher()
            ->where('student_id', $data['student_id'])
            ->where('subject_id', $data['subject_id'])
            ->where('class_id', $data['class_id'])
            ->where('academic_session', $data['academic_session'])
            ->where('term', $data['term']);

        if ($ignoreId) {
            $weightQuery->where('id', '!=', $ignoreId);
        }

        $currentWeight = (float) $weightQuery->sum('weight');
        $newWeight = $currentWeight + (float) ($data['weight'] ?? 0);

        if ($newWeight > 100.0001) {
            throw new GradeWeightingException();
        }
    }

    protected function assertTeacherCanAccessSubject(string $subjectId): void
    {
        $subject = Subject::query()->teacher()->find($subjectId);

        if (! $subject) {
            throw new AuthorizationException('You are not allowed to manage gradebook entries for this subject.');
        }
    }
}
