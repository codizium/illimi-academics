<?php

namespace Illimi\Academics\Services;

use Illimi\Academics\Events\GradeAppealResolved;
use Illimi\Academics\Events\GradeAppealSubmitted;
use Illimi\Academics\Models\GradeAppeal;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Event;

class GradeAppealService
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = GradeAppeal::query()->with(['student', 'result.student', 'result.academicClass']);

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['result_id'])) {
            $query->where('result_id', $filters['result_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function submit(array $data): GradeAppeal
    {
        $appeal = GradeAppeal::create([
            'result_id' => $data['result_id'],
            'student_id' => $data['student_id'] ?? null,
            'reason' => $data['reason'],
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        Event::dispatch(new GradeAppealSubmitted($appeal));

        return $this->findById($appeal->id) ?? $appeal->fresh();
    }

    public function resolve(string $id, array $data, string $resolvedBy): ?GradeAppeal
    {
        $appeal = GradeAppeal::find($id);

        if (! $appeal) {
            return null;
        }

        $appeal->update([
            'status' => $data['status'] ?? 'resolved',
            'resolution' => $data['resolution'] ?? null,
            'resolved_by' => $resolvedBy,
            'resolved_at' => now(),
        ]);

        Event::dispatch(new GradeAppealResolved($appeal));

        return $this->findById($appeal->id) ?? $appeal->fresh();
    }

    public function findById(string $id): ?GradeAppeal
    {
        return GradeAppeal::query()
            ->with(['student', 'result.student', 'result.academicClass'])
            ->find($id);
    }

    public function delete(string $id): bool
    {
        $appeal = GradeAppeal::find($id);

        if (! $appeal) {
            return false;
        }

        return (bool) $appeal->delete();
    }
}
