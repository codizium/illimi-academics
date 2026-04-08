<?php

namespace Illimi\Academics\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Traits\BelongsToOrganization;
use Codizium\Core\Traits\HasCuid;
use Illimi\Academics\Traits\HasAcademicYear;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicTerm extends BaseModel
{
    use BelongsToOrganization, HasCuid, SoftDeletes, HasAcademicYear;

    protected $table = 'illimi_academic_terms';
    protected $academic_year_field = 'academic_year_id';

    protected $fillable = [
        'organization_id',
        'academic_year_id',
        'name',
        'slug',
        'description',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'organization_id' => 'string',
        'academic_year_id' => 'string',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}
