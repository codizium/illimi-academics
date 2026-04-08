<?php

namespace Illimi\Academics\Events;

use Illimi\Academics\Models\ExamAttempt;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExamAutoGraded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ExamAttempt $attempt
    ) {
    }
}
