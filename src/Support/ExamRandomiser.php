<?php

namespace Illimi\Academics\Support;

class ExamRandomiser
{
    public static function shuffleQuestions(array $questions, bool $randomise = true, ?string $seed = null): array
    {
        if (! $randomise) {
            return $questions;
        }

        $shuffled = $questions;
        $seeded = $seed !== null;

        if ($seeded) {
            mt_srand(crc32($seed));
        }

        shuffle($shuffled);

        if ($seeded) {
            mt_srand();
        }

        return $shuffled;
    }

    public static function shuffleOptions(array $options, bool $randomise = true, ?string $seed = null): array
    {
        if (! $randomise) {
            return $options;
        }

        $shuffled = $options;
        $seeded = $seed !== null;

        if ($seeded) {
            mt_srand(crc32($seed));
        }

        shuffle($shuffled);

        if ($seeded) {
            mt_srand();
        }

        return $shuffled;
    }
}
