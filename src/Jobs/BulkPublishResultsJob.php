<?php

namespace Illimi\Academics\Jobs;

use Illimi\Academics\Services\ResultService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BulkPublishResultsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $resultIds,
        public string $publishedBy
    ) {
    }

    public function handle(ResultService $resultService): void
    {
        $resultService->publish($this->resultIds, $this->publishedBy);
    }
}
