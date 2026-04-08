<?php

namespace Illimi\Academics\Events;

use Illimi\Academics\Models\GradeAppeal;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GradeAppealResolved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public GradeAppeal $appeal
    ) {
    }
}
