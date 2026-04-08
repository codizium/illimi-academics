<?php

namespace Illimi\Academics\Events;

use Illimi\Academics\Models\Result;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResultsDrafted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Result $result
    ) {
    }
}
