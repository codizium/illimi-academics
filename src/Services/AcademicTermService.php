<?php

namespace Illimi\Academics\Services;

use Illimi\Academics\Models\AcademicTerm;
use Illuminate\Pagination\LengthAwarePaginator;

class AcademicTermService
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = AcademicTerm::query();

        if (!empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): AcademicTerm
    {
        return AcademicTerm::create($data);
    }

    public function update(string $id, array $data): ?AcademicTerm
    {
        $term = AcademicTerm::find($id);

        if (! $term) {
            return null;
        }

        $term->update($data);

        return $term->fresh();
    }

    public function findById(string $id): ?AcademicTerm
    {
        return AcademicTerm::query()
            ->with('academicYear')
            ->find($id);
    }

    public function delete(string $id): bool
    {
        $term = AcademicTerm::find($id);

        if (! $term) {
            return false;
        }

        return (bool) $term->delete();
    }
}
