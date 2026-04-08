<?php

namespace Illimi\Academics\Events;

use Illimi\Academics\Models\Transcript;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TranscriptGenerated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Transcript $transcript
    ) {
    }
}
