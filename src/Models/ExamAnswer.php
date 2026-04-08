<?php

namespace Illimi\Academics\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Traits\BelongsToOrganization;
use Codizium\Core\Traits\HasCuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamAnswer extends BaseModel
{
    use BelongsToOrganization, HasCuid, SoftDeletes;

    protected $table = 'illimi_exam_answers';

    protected $fillable = [
        'organization_id',
        'exam_attempt_id',
        'question_id',
        'answer',
        'is_correct',
        'marks_awarded',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'exam_attempt_id' => 'string',
        'question_id' => 'string',
        'is_correct' => 'boolean',
        'marks_awarded' => 'decimal:2',
    ];

    public function examAttempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
