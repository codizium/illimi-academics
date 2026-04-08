<?php

namespace Illimi\Academics\Support;

use Illuminate\Support\Collection;

class GradeCalculator
{
    public static function calculateWeightedScore($entries): float
    {
        $collection = $entries instanceof Collection ? $entries : collect($entries);

        $total = $collection->sum(function ($entry) {
            if ((float) $entry->max_score <= 0) {
                return 0;
            }

            return ((float) $entry->score / (float) $entry->max_score) * (float) $entry->weight;
        });

        return round($total, 2);
    }
}
