<?php

namespace Illimi\Academics\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Traits\BelongsToOrganization;
use Codizium\Core\Traits\HasAttachment;
use Codizium\Core\Traits\HasCuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Syllabus extends BaseModel
{
    use BelongsToOrganization, HasCuid, SoftDeletes, HasAttachment;

    protected $table = 'illimi_syllabi';

    protected $fillable = [
        'organization_id',
        'subject_id',
        'title',
        'description',
        'content',
        'objectives',
        'topics',
        'is_published',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'subject_id' => 'string',
        'is_published' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
