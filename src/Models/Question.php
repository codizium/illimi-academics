<?php

namespace Illimi\Academics\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Traits\BelongsToOrganization;
use Codizium\Core\Traits\HasCuid;
use Illimi\Academics\Enums\QuestionTypeEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends BaseModel
{
    use BelongsToOrganization, HasCuid, SoftDeletes;

    protected $table = 'illimi_questions';

    protected $fillable = [
        'organization_id',
        'question_bank_id',
        'subject_id',
        'type',
        'content',
        'options',
        'correct_answer',
        'explanation',
        'difficulty',
        'marks',
        'tags',
        'curriculum_ref',
        'created_by',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'question_bank_id' => 'string',
        'subject_id' => 'string',
        'created_by' => 'string',
        'type' => QuestionTypeEnum::class,
        'options' => 'encrypted:array',
        'correct_answer' => 'encrypted',
        'marks' => 'decimal:2',
        'tags' => 'array',
    ];

    public function questionBank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\Codizium\Core\Models\User::class, 'created_by');
    }

    public function examAnswers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class, 'question_id');
    }

    protected static function newFactory()
    {
        return \Illimi\Academics\Database\Factories\QuestionFactory::new();
    }
}
