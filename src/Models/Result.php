<?php

namespace Illimi\Academics\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Traits\BelongsToOrganization;
use Codizium\Core\Traits\HasCuid;
use Illimi\Academics\Enums\ResultStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Result extends BaseModel
{
    use BelongsToOrganization, HasCuid, SoftDeletes;

    protected $table = 'illimi_results';

    protected $fillable = [
        'organization_id',
        'student_id',
        'class_id',
        'academic_session',
        'term',
        'total_score',
        'grade',
        'grade_point',
        'remark',
        'position_in_class',
        'status',
        'published_by',
        'published_at',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'student_id' => 'string',
        'class_id' => 'string',
        'published_by' => 'string',
        'total_score' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'position_in_class' => 'integer',
        'status' => ResultStatusEnum::class,
        'published_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Illimi\Students\Models\Student::class, 'student_id');
    }

    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }

    public function appeals(): HasMany
    {
        return $this->hasMany(GradeAppeal::class, 'result_id');
    }

    protected static function newFactory()
    {
        return \Illimi\Academics\Database\Factories\ResultFactory::new();
    }
}
