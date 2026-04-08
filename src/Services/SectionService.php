<?php

namespace Illimi\Academics\Services;

use Illimi\Academics\Models\Section;
use Illuminate\Pagination\LengthAwarePaginator;

class SectionService
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Section::query();

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): Section
    {
        return Section::create($data);
    }

    public function update(string $id, array $data): ?Section
    {
        $section = Section::find($id);

        if (!$section) {
            return null;
        }

        $section->update($data);

        return $this->findById($section->id);
    }

    public function findById(string $id): ?Section
    {
        return Section::query()->find($id);
    }

    public function delete(string $id): bool
    {
        $section = Section::find($id);

        if (!$section) {
            return false;
        }

        return (bool) $section->delete();
    }
}
