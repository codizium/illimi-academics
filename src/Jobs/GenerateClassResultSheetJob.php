<?php

namespace Illimi\Academics\Jobs;

use Illimi\Academics\Services\ResultService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateClassResultSheetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $classId,
        public string $academicSession,
        public string $term
    ) {
    }

    public function handle(ResultService $resultService): void
    {
        $resultService->list([
            'class_id' => $this->classId,
            'academic_session' => $this->academicSession,
            'term' => $this->term,
        ], 500);
    }
}
