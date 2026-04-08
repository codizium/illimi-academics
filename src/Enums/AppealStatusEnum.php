<?php

namespace Illimi\Academics\Enums;

/**
 * Grade Appeal Status Enum
 */
enum AppealStatusEnum: string
{
    case Submitted = 'submitted';
    case UnderReview = 'under_review';
    case Resolved = 'resolved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Submitted => 'Submitted',
            self::UnderReview => 'Under Review',
            self::Resolved => 'Resolved',
            self::Rejected => 'Rejected',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
