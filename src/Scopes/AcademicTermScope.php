<?php
namespace Illimi\Academics\Scopes;

use Illimi\Academics\Models\AcademicTerm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;


class AcademicTermScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (!app()->bound(AcademicTerm::class)) {
            return;
        }

        /** @var AcademicTerm|null $acad */
        $acad = app(AcademicTerm::class);

        if ($acad && $acad->id) {
            $builder->where($model->qualifyColumn('academic_term_id'), $acad->id);
        }
    }
}
