<?php

namespace Illimi\Academics\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Traits\BelongsToOrganization;
use Codizium\Core\Traits\HasCuid;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends BaseModel
{
    use BelongsToOrganization, HasCuid, SoftDeletes;

    protected $table = 'illimi_classrooms';

    protected $fillable = [
        'organization_id',
        'name',
        'code',
        'capacity',
        'location',
        'description',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'capacity' => 'integer',
    ];

    public function academicClasses(): HasMany
    {
        return $this->hasMany(AcademicClass::class, 'classroom_id');
    }
}
