<?php

namespace Illimi\Academics\Console;

use Illimi\Academics\Services\ResultService;
use Illuminate\Console\Command;

class PublishResultsCommand extends Command
{
    protected $signature = 'academics:publish-results {result_ids*} {--published-by=}';

    protected $description = 'Publish results by their IDs';

    public function handle(ResultService $resultService): int
    {
        $resultIds = (array) $this->argument('result_ids');
        $publishedBy = $this->option('published-by') ?: 'system';

        $count = $resultService->publish($resultIds, $publishedBy);

        $this->info("Published {$count} results.");

        return self::SUCCESS;
    }
}
