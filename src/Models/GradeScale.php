<?php

namespace Illimi\Academics\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Traits\BelongsToOrganization;
use Codizium\Core\Traits\HasCuid;
use Illuminate\Database\Eloquent\SoftDeletes;

class GradeScale extends BaseModel
{
    use BelongsToOrganization, HasCuid, SoftDeletes;

    protected $table = 'illimi_grade_scales';

    protected $fillable = [
        'organization_id',
        'name',
        'code',
        'min_score',
        'max_score',
        'description',
        'is_default',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'min_score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'is_default' => 'boolean',
    ];
}
