<?php

namespace Illimi\Academics\Services;

use Illimi\Academics\Models\GradeScale;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class GradeScaleService
{
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return GradeScale::query()
            ->orderByDesc('is_default')
            ->orderByDesc('max_score')
            ->orderByDesc('min_score')
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): GradeScale
    {
        $this->ensureScoreRangeIsUnique($data);

        return GradeScale::create($data);
    }

    public function update(string $id, array $data): ?GradeScale
    {
        $gradeScale = GradeScale::find($id);

        if (! $gradeScale) {
            return null;
        }

        $this->ensureScoreRangeIsUnique($data, $gradeScale);

        $gradeScale->update($data);

        return $gradeScale->fresh();
    }

    public function findById(string $id): ?GradeScale
    {
        return GradeScale::find($id);
    }

    public function delete(string $id): bool
    {
        $gradeScale = GradeScale::find($id);

        if (! $gradeScale) {
            return false;
        }

        return (bool) $gradeScale->delete();
    }

    protected function ensureScoreRangeIsUnique(array $data, ?GradeScale $existing = null): void
    {
        $minScore = array_key_exists('min_score', $data)
            ? (float) $data['min_score']
            : ($existing?->min_score !== null ? (float) $existing->min_score : null);
        $maxScore = array_key_exists('max_score', $data)
            ? (float) $data['max_score']
            : ($existing?->max_score !== null ? (float) $existing->max_score : null);

        if ($minScore === null || $maxScore === null) {
            return;
        }

        $conflictingScale = GradeScale::query()
            ->when($existing, fn ($query) => $query->whereKeyNot($existing->id))
            ->whereNotNull('min_score')
            ->whereNotNull('max_score')
            ->where('min_score', '<=', $maxScore)
            ->where('max_score', '>=', $minScore)
            ->first();

        if (! $conflictingScale) {
            return;
        }

        throw ValidationException::withMessages([
            'min_score' => [sprintf(
                'This score range overlaps with grade scale "%s" (%s - %s). Each grade scale must have a unique score ranking.',
                $conflictingScale->name,
                rtrim(rtrim(number_format((float) $conflictingScale->min_score, 2, '.', ''), '0'), '.'),
                rtrim(rtrim(number_format((float) $conflictingScale->max_score, 2, '.', ''), '0'), '.')
            )],
        ]);
    }
}
