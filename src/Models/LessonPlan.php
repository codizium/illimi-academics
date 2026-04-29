<?php

namespace Illimi\Academics\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Traits\BelongsToOrganization;
use Codizium\Core\Traits\HasAttachment;
use Codizium\Core\Traits\HasCuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonPlan extends BaseModel
{
    use BelongsToOrganization, HasCuid, SoftDeletes, HasAttachment;

    protected $table = 'illimi_lesson_plans';

    protected $fillable = [
        'organization_id',
        'scheme_of_work_id',
        'teacher_id',
        'date',
        'duration_minutes',
        'topic',
        'learning_outcomes',
        'introduction',
        'presentation_steps',
        'evaluation',
        'conclusion',
        'teaching_aids',
        'status',
        'is_completed',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'scheme_of_work_id' => 'string',
        'teacher_id' => 'string',
        'date' => 'date',
        'duration_minutes' => 'integer',
        'learning_outcomes' => 'array',
        'presentation_steps' => 'array',
        'teaching_aids' => 'array',
        'is_completed' => 'boolean',
    ];

    public function schemeOfWork(): BelongsTo
    {
        return $this->belongsTo(SchemeOfWork::class, 'scheme_of_work_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(\Illimi\Staff\Models\Staff::class, 'teacher_id');
    }
}
