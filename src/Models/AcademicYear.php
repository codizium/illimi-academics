<?php
namespace Illimi\Academics\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Traits\BelongsToOrganization;
use Codizium\Core\Traits\HasCuid;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends BaseModel
{
    use BelongsToOrganization, HasCuid, SoftDeletes;

    protected $table = 'illimi_academic_years';

    protected $fillable = [
        'organization_id',
        'name',
        'slug',
        'description',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function terms(): HasMany
    {
        return $this->hasMany(AcademicTerm::class, 'academic_year_id');
    }
}
