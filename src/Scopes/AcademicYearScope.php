<?php
namespace Illimi\Academics\Scopes;

use Illimi\Academics\Models\AcademicYear;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;


class AcademicYearScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (!app()->bound(AcademicYear::class)) {
            return;
        }

        /** @var AcademicYear|null $acad */
        $acad = app(AcademicYear::class);
        

        if ($acad && $acad->id) {
            $builder->where($model->qualifyColumn('academic_year_id'), $acad->id);
        }
    }
}
