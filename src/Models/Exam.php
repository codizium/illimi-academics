<?php

namespace Illimi\Academics\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Traits\BelongsToOrganization;
use Codizium\Core\Traits\HasCuid;
use Illimi\Academics\Enums\ExamStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends BaseModel
{
    use BelongsToOrganization, HasCuid, SoftDeletes;

    protected $table = 'illimi_exams';

    protected $fillable = [
        'organization_id',
        'title',
        'subject_id',
        'class_id',
        'academic_session',
        'term',
        'duration_minutes',
        'total_marks',
        'pass_mark',
        'negative_marking',
        'negative_mark_value',
        'randomise_questions',
        'randomise_options',
        'allow_review',
        'status',
        'starts_at',
        'ends_at',
        'created_by',
        'proctoring_options',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'subject_id' => 'string',
        'class_id' => 'string',
        'created_by' => 'string',
        'status' => ExamStatusEnum::class,
        'duration_minutes' => 'integer',
        'total_marks' => 'decimal:2',
        'pass_mark' => 'decimal:2',
        'negative_marking' => 'boolean',
        'negative_mark_value' => 'decimal:2',
        'randomise_questions' => 'boolean',
        'randomise_options' => 'boolean',
        'allow_review' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'proctoring_options' => 'array',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\Codizium\Core\Models\User::class, 'created_by');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class, 'exam_id');
    }

    protected static function newFactory()
    {
        return \Illimi\Academics\Database\Factories\ExamFactory::new();
    }
}
