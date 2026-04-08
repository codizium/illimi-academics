<?php
namespace Illimi\Academics\Traits;

use Illimi\Academics\Models\AcademicTerm;
use Illimi\Academics\Scopes\AcademicTermScope;



trait HasAcademicTerm
{
    protected string $academic_term_field = 'academic_term_id';
    protected static function bootHasAcademicTerm()
    {
        // Auto filter queries
        static::addGlobalScope(new AcademicTermScope());

        // Auto set on create
        static::creating(function ($model) {
            if (app()->bound(AcademicTerm::class)) {
                $model->academic_term_id = app(AcademicTerm::class)->id;
            }
        });

        // Auto enforce on update
        static::saving(function ($model) {
            if (app()->bound(AcademicTerm::class)) {
                $model->academic_term_id = app(AcademicTerm::class)->id;
            }
        });
    }

    // Relationship (optional but recommended)
    public function academicTerm()
    {
        return $this->belongsTo(AcademicTerm::class, $this->academic_term_field, 'id');
    }
    public function term()
    {
        return $this->belongsTo(AcademicTerm::class, $this->academic_term_field, 'id');
    }
}
