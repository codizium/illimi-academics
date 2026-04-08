<?php

namespace Illimi\Academics\Listeners;

use Illimi\Academics\Events\ResultsPublished;
use Illimi\Academics\Models\Result;
use Illimi\Academics\Services\TranscriptService;

class TriggerTranscriptGeneration
{
    public function handle(ResultsPublished $event): void
    {
        $results = Result::query()->whereIn('id', $event->resultIds)->get();

        foreach ($results as $result) {
            app(TranscriptService::class)->generateTranscript(
                $result->student_id,
                $result->academic_session,
                $result->term
            );
        }
    }
}
