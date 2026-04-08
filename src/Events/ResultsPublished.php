<?php

namespace Illimi\Academics\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResultsPublished
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param array $resultIds
     * @param string $publishedBy
     */
    public function __construct(
        public array $resultIds,
        public string $publishedBy
    ) {
    }
}
