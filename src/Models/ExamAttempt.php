<?php

namespace Illimi\Academics\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Traits\BelongsToOrganization;
use Codizium\Core\Traits\HasCuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamAttempt extends BaseModel
{
    use BelongsToOrganization, HasCuid, SoftDeletes;

    protected $table = 'illimi_exam_attempts';

    protected $fillable = [
        'organization_id',
        'exam_id',
        'student_id',
        'started_at',
        'submitted_at',
        'time_taken_seconds',
        'score',
        'is_auto_graded',
        'status',
        'ip_address',
        'browser_info',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'exam_id' => 'string',
        'student_id' => 'string',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'time_taken_seconds' => 'integer',
        'score' => 'decimal:2',
        'is_auto_graded' => 'boolean',
        'browser_info' => 'array',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Illimi\Students\Models\Student::class, 'student_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class, 'exam_attempt_id');
    }
}
