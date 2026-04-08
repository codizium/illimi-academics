<?php

namespace Illimi\Academics\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Traits\BelongsToOrganization;
use Codizium\Core\Traits\HasCuid;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends BaseModel
{
    use BelongsToOrganization, HasCuid, SoftDeletes;

    protected $table = 'illimi_sections';

    protected $fillable = [
        'organization_id',
        'name',
        'description',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
    ];

    public function classes(): HasMany
    {
        return $this->hasMany(AcademicClass::class, 'section_id');
    }
}
