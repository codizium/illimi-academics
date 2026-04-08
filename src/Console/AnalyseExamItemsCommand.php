<?php

namespace Illimi\Academics\Console;

use Illimi\Academics\Services\ItemAnalysisService;
use Illuminate\Console\Command;

class AnalyseExamItemsCommand extends Command
{
    protected $signature = 'academics:analyse-exam-items {exam_id}';

    protected $description = 'Run item analysis for an exam';

    public function handle(ItemAnalysisService $itemAnalysisService): int
    {
        $examId = (string) $this->argument('exam_id');

        $analysis = $itemAnalysisService->analyseExam($examId);

        $this->info('Item analysis completed.');
        $this->line(json_encode($analysis, JSON_PRETTY_PRINT));

        return self::SUCCESS;
    }
}
