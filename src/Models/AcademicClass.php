<?php

namespace Illimi\Academics\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Traits\BelongsToOrganization;
use Codizium\Core\Traits\HasCuid;
use Illimi\Students\Models\Student;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicClass extends BaseModel
{
    use BelongsToOrganization, HasCuid, SoftDeletes;

    protected $table = 'illimi_classes';

    protected $fillable = [
        'organization_id',
        'name',
        'level',
        'section_id',
        'classroom_id',
        'class_teacher_id',
        'capacity',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'section_id' => 'string',
        'classroom_id' => 'string',
        'class_teacher_id' => 'string',
        'capacity' => 'integer',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id', 'id');
    }

    public function classTeacher(): BelongsTo
    {
        return $this->belongsTo(\Illimi\Staff\Models\Staff::class, 'class_teacher_id');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'illimi_class_subject', 'class_id', 'subject_id')
            ->withPivot('organization_id')
            ->withTimestamps();
    }

    public function gradebookEntries(): HasMany
    {
        return $this->hasMany(GradebookEntry::class, 'class_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class, 'class_id');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'class_id');
    }
}
