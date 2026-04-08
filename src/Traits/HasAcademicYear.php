<?php
namespace Illimi\Academics\Traits;

use Illimi\Academics\Models\AcademicYear;
use Illimi\Academics\Scopes\AcademicYearScope;



trait HasAcademicYear
{
    protected $academic_year_field = 'academic_year_id';
    protected static function bootHasAcademicYear()
    {
        // Auto filter queries
        static::addGlobalScope(new AcademicYearScope());

        // Auto set on create
        static::creating(function ($model) {
            if (app()->bound(AcademicYear::class)) {
                $model->academic_year_id = app(AcademicYear::class)->id;
            }
        });

        // Auto enforce on update
        static::saving(function ($model) {
            if (app()->bound(AcademicYear::class)) {
                $model->academic_year_id = app(AcademicYear::class)->id;
            }
        });
    }

    // Relationship (optional but recommended)
    public function academic_year()
    {
        return $this->belongsTo(AcademicYear::class, $this->organization_field, 'id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, $this->organization_field, 'id');
    }
}
