<?php

namespace Illimi\Academics\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Traits\BelongsToOrganization;
use Codizium\Core\Traits\HasCuid;
use Illimi\Academics\Enums\GradeComponentEnum;
use Illimi\Academics\Scopes\TeacherScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GradebookEntry extends BaseModel
{
    use BelongsToOrganization, HasCuid, SoftDeletes;

    protected $table = 'illimi_gradebook_entries';

    protected $fillable = [
        'organization_id',
        'student_id',
        'subject_id',
        'class_id',
        'academic_session',
        'term',
        'component',
        'score',
        'max_score',
        'weight',
        'entered_by',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'student_id' => 'string',
        'subject_id' => 'string',
        'class_id' => 'string',
        'entered_by' => 'string',
        'component' => GradeComponentEnum::class,
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Illimi\Students\Models\Student::class, 'student_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(\Codizium\Core\Models\User::class, 'entered_by');
    }

    public function scopeTeacher(Builder $query, $user = null): Builder
    {
        return TeacherScope::apply($query, $user, 'subject.teachers');
    }
}
