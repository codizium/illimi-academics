<?php

namespace Illimi\Academics\Services;

use Illimi\Academics\Models\Classroom;
use Illuminate\Pagination\LengthAwarePaginator;

class ClassroomService
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Classroom::query()->withCount('academicClasses');

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): Classroom
    {
        return Classroom::create($data);
    }

    public function update(string $id, array $data): ?Classroom
    {
        $classroom = Classroom::find($id);

        if (!$classroom) {
            return null;
        }

        $classroom->update($data);

        return $this->findById($classroom->id);
    }

    public function findById(string $id): ?Classroom
    {
        return Classroom::query()
            ->withCount('academicClasses')
            ->find($id);
    }

    public function delete(string $id): bool
    {
        $classroom = Classroom::find($id);

        if (!$classroom) {
            return false;
        }

        return (bool) $classroom->delete();
    }
}
