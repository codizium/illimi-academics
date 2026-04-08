<?php

namespace Illimi\Academics\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Traits\BelongsToOrganization;
use Codizium\Core\Traits\HasCuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transcript extends BaseModel
{
    use BelongsToOrganization, HasCuid, SoftDeletes;

    protected $table = 'illimi_transcripts';

    protected $fillable = [
        'organization_id',
        'student_id',
        'academic_session',
        'term',
        'file_path',
        'payload',
        'generated_at',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'student_id' => 'string',
        'payload' => 'array',
        'generated_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Illimi\Students\Models\Student::class, 'student_id');
    }
}
