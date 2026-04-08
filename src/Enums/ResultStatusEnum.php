<?php

namespace Illimi\Academics\Enums;

/**
 * Result Status Enum
 */
enum ResultStatusEnum: string
{
    case Draft = 'draft';
    case UnderReview = 'under_review';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::UnderReview => 'Under Review',
            self::Published => 'Published',
            self::Archived => 'Archived',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
