<?php

namespace Illimi\Academics\Enums;

/**
 * Grade Component Enum
 */
enum GradeComponentEnum: string
{
    case Exam = 'exam';
    case ContinuousAssessment = 'continuous_assessment';
    case Practical = 'practical';
    case Project = 'project';

    public function label(): string
    {
        return match ($this) {
            self::Exam => 'Exam',
            self::ContinuousAssessment => 'Continuous Assessment',
            self::Practical => 'Practical',
            self::Project => 'Project',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
