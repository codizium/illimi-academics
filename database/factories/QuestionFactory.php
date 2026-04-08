<?php

namespace Illimi\Academics\Database\Factories;

use Illimi\Academics\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'organization_id' => (string) Str::uuid(),
            'question_bank_id' => (string) Str::uuid(),
            'subject_id' => (string) Str::uuid(),
            'type' => 'mcq',
            'content' => $this->faker->sentence(12),
            'options' => ['A', 'B', 'C', 'D'],
            'correct_answer' => 'A',
            'explanation' => $this->faker->sentence(),
            'difficulty' => 'medium',
            'marks' => 1,
            'tags' => [],
            'curriculum_ref' => null,
            'created_by' => (string) Str::uuid(),
        ];
    }
}
