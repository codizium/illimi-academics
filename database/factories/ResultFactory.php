<?php

namespace Illimi\Academics\Database\Factories;

use Illimi\Academics\Models\Result;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ResultFactory extends Factory
{
    protected $model = Result::class;

    public function definition(): array
    {
        return [
            'organization_id' => (string) Str::uuid(),
            'student_id' => (string) Str::uuid(),
            'class_id' => (string) Str::uuid(),
            'academic_session' => '2025/2026',
            'term' => 'Term 1',
            'total_score' => 75,
            'grade' => 'A',
            'grade_point' => 4.0,
            'remark' => 'Excellent',
            'position_in_class' => 1,
            'status' => 'under_review',
            'published_by' => null,
            'published_at' => null,
        ];
    }
}
