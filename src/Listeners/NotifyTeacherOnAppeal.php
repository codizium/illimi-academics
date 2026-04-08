<?php

namespace Illimi\Academics\Listeners;

use Illimi\Academics\Events\GradeAppealSubmitted;

class NotifyTeacherOnAppeal
{
    public function handle(GradeAppealSubmitted $event): void
    {
        // In production, notify subject teacher/HOD about a new appeal.
    }
}
