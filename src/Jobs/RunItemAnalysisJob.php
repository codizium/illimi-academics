<?php

namespace Illimi\Academics\Jobs;

use Illimi\Academics\Services\ItemAnalysisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunItemAnalysisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $examId)
    {
    }

    public function handle(ItemAnalysisService $itemAnalysisService): void
    {
        $itemAnalysisService->analyseExam($this->examId);
    }
}
