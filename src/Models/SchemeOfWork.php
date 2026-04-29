<?php

namespace Illimi\Academics\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Traits\BelongsToOrganization;
use Codizium\Core\Traits\HasAttachment;
use Codizium\Core\Traits\HasCuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchemeOfWork extends BaseModel
{
    use BelongsToOrganization, HasCuid, SoftDeletes, HasAttachment;

    protected $table = 'illimi_schemes_of_work';

    protected $fillable = [
        'organization_id',
        'syllabus_id',
        'academic_year_id',
        'academic_term_id',
        'teacher_id',
        'class_id',
        'week_number',
        'topic',
        'sub_topics',
        'learning_objectives',
        'teaching_aids',
        'assessment_methods',
        'remarks',
        'status',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'syllabus_id' => 'string',
        'academic_year_id' => 'string',
        'academic_term_id' => 'string',
        'teacher_id' => 'string',
        'class_id' => 'string',
        'week_number' => 'integer',
        'sub_topics' => 'array',
        'learning_objectives' => 'array',
        'teaching_aids' => 'array',
        'assessment_methods' => 'array',
    ];

    public function syllabus(): BelongsTo
    {
        return $this->belongsTo(Syllabus::class, 'syllabus_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class, 'academic_term_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(\Illimi\Staff\Models\Staff::class, 'teacher_id');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }

    public function lessonPlans(): HasMany
    {
        return $this->hasMany(LessonPlan::class, 'scheme_of_work_id');
    }
}
