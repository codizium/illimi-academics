<?php

namespace Illimi\Academics\Jobs;

use Illimi\Academics\Models\ExamAttempt;
use Illimi\Academics\Services\GradingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoGradeExamJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $attemptId)
    {
    }

    public function handle(GradingService $gradingService): void
    {
        $attempt = ExamAttempt::find($this->attemptId);

        if (! $attempt) {
            return;
        }

        $gradingService->autoGrade($attempt);
    }
}
