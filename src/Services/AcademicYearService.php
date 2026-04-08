<?php

namespace Illimi\Academics\Services;

use Illimi\Academics\Models\AcademicYear;
use Illuminate\Pagination\LengthAwarePaginator;

class AcademicYearService
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = AcademicYear::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%'.$filters['name'].'%');
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): AcademicYear
    {
        return AcademicYear::create($data);
    }

    public function update(string $id, array $data): ?AcademicYear
    {
        $year = AcademicYear::find($id);

        if (! $year) {
            return null;
        }

        $year->update($data);

        return $year->fresh();
    }

    public function findById(string $id): ?AcademicYear
    {
        return AcademicYear::query()
            ->withCount('terms')
            ->find($id);
    }

    public function delete(string $id): bool
    {
        $year = AcademicYear::find($id);

        if (! $year) {
            return false;
        }

        return (bool) $year->delete();
    }
}
