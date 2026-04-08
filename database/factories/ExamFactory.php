<?php

namespace Illimi\Academics\Database\Factories;

use Illimi\Academics\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ExamFactory extends Factory
{
    protected $model = Exam::class;

    public function definition(): array
    {
        return [
            'organization_id' => (string) Str::uuid(),
            'title' => $this->faker->sentence(3),
            'subject_id' => (string) Str::uuid(),
            'class_id' => (string) Str::uuid(),
            'academic_session' => '2025/2026',
            'term' => 'Term 1',
            'duration_minutes' => 60,
            'total_marks' => 100,
            'pass_mark' => 50,
            'negative_marking' => false,
            'negative_mark_value' => null,
            'randomise_questions' => true,
            'randomise_options' => true,
            'allow_review' => true,
            'status' => 'scheduled',
            'starts_at' => now(),
            'ends_at' => now()->addHour(),
            'created_by' => (string) Str::uuid(),
            'proctoring_options' => [],
        ];
    }
}
