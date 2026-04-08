<?php

namespace Illimi\Academics\Enums;

/**
 * Question Type Enum
 */
enum QuestionTypeEnum: string
{
    case Mcq = 'mcq';
    case TrueFalse = 'true_false';
    case ShortAnswer = 'short_answer';
    case CodeSnippet = 'code_snippet';

    public function label(): string
    {
        return match ($this) {
            self::Mcq => 'Multiple Choice',
            self::TrueFalse => 'True/False',
            self::ShortAnswer => 'Short Answer',
            self::CodeSnippet => 'Code Snippet',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
