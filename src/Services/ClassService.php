<?php

namespace Illimi\Academics\Services;

use Illimi\Academics\Models\AcademicClass;
use Illuminate\Pagination\LengthAwarePaginator;

class ClassService
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = AcademicClass::query();

        if (!empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }


        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): AcademicClass
    {
        return AcademicClass::create($data);
    }

    public function update(string $id, array $data): ?AcademicClass
    {
        $class = AcademicClass::find($id);

        if (!$class) {
            return null;
        }

        $class->update($data);

        return $this->findById($class->id);
    }

    public function findById(string $id): ?AcademicClass
    {
        return AcademicClass::query()
            ->with('classTeacher', 'section', 'classroom')
            ->find($id);
    }

    public function delete(string $id): bool
    {
        $class = AcademicClass::find($id);

        if (!$class) {
            return false;
        }

        return (bool) $class->delete();
    }
}
