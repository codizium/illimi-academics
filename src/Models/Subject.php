<?php

namespace Illimi\Academics\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Traits\BelongsToOrganization;
use Codizium\Core\Traits\HasCuid;
use Illimi\Academics\Scopes\TeacherScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends BaseModel
{
    use BelongsToOrganization, HasCuid, SoftDeletes;

    protected $table = 'illimi_subjects';

    protected $fillable = [
        'organization_id',
        'name',
        'code',
        'description',
        'is_compulsory',
        'credit_units',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'is_compulsory' => 'boolean',
        'credit_units' => 'integer',
    ];

    public function gradebookEntries(): HasMany
    {
        return $this->hasMany(GradebookEntry::class, 'subject_id');
    }

    public function scopeTeacher(Builder $query, $user = null): Builder
    {
        return TeacherScope::apply($query, $user);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(\Illimi\Staff\Models\Staff::class, 'illimi_staff_subject', 'subject_id', 'staff_id')
            ->withPivot('organization_id')
            ->withTimestamps();
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(AcademicClass::class, 'illimi_class_subject', 'subject_id', 'class_id')
            ->withPivot('organization_id')
            ->withTimestamps();
    }

    public function questionBanks(): HasMany
    {
        return $this->hasMany(QuestionBank::class, 'subject_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'subject_id');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'subject_id');
    }

    public function syllabi(): HasMany
    {
        return $this->hasMany(Syllabus::class, 'subject_id');
    }

    public function currentSyllabus(): HasOne
    {
        return $this->hasOne(Syllabus::class, 'subject_id')->latestOfMany();
    }
}
