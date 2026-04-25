<?php

namespace Illimi\Academics\Controllers\Web;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class AcademicsWebController
{
    protected function organizationId(): ?string
    {
        return optional(function_exists('organization') ? organization() : null)->id
            ?? auth()->user()?->organization_id;
    }

    protected function queryFor(string $modelClass): Builder
    {
        $query = $modelClass::query();

        // Apply role-based scopes in priority order.
        // TeacherScope is checked first because a Teacher who is also a Parent
        // should use Teacher-level visibility in the academic context.
        if (method_exists($modelClass, 'scopeTeacher')) {
            $query->teacher();
        } elseif (method_exists($modelClass, 'scopeStudent')) {
            $query->student();
        } elseif (method_exists($modelClass, 'scopeParent')) {
            $query->parent();
        }

        return $query->when(
            $this->organizationId(),
            fn (Builder $query, string $organizationId) => $query->where('organization_id', $organizationId)
        );
    }

    protected function findForEdit(Request $request, string $modelClass, array $with = []): ?Model
    {
        $id = $request->query('id');

        if (! $id) {
            return null;
        }

        return $this->queryFor($modelClass)->with($with)->find($id);
    }
}
