<?php

namespace Illimi\Academics\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Traits\BelongsToOrganization;
use Codizium\Core\Traits\HasCuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionBank extends BaseModel
{
    use BelongsToOrganization, HasCuid, SoftDeletes;

    protected $table = 'illimi_question_banks';

    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'subject_id',
        'class_id',
        'created_by',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'subject_id' => 'string',
        'class_id' => 'string',
        'created_by' => 'string',
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

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'question_bank_id');
    }
}
