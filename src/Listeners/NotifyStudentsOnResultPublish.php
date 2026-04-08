<?php

namespace Illimi\Academics\Listeners;

use Illimi\Academics\Events\ResultsPublished;

class NotifyStudentsOnResultPublish
{
    public function handle(ResultsPublished $event): void
    {
        // In production, notify students/parents about published results.
        // This could send email, SMS, push, or in-app notifications.
    }
}
