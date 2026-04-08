<?php

namespace Illimi\Academics\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Traits\BelongsToOrganization;
use Codizium\Core\Traits\HasCuid;
use Illimi\Academics\Enums\AppealStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GradeAppeal extends BaseModel
{
    use BelongsToOrganization, HasCuid, SoftDeletes;

    protected $table = 'illimi_grade_appeals';

    protected $fillable = [
        'organization_id',
        'result_id',
        'student_id',
        'reason',
        'status',
        'submitted_at',
        'resolved_at',
        'resolution',
        'resolved_by',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'result_id' => 'string',
        'student_id' => 'string',
        'resolved_by' => 'string',
        'status' => AppealStatusEnum::class,
        'submitted_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function result(): BelongsTo
    {
        return $this->belongsTo(Result::class, 'result_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Illimi\Students\Models\Student::class, 'student_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(\Codizium\Core\Models\User::class, 'resolved_by');
    }
}
