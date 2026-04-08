<?php

namespace Illimi\Academics\Console;

use Illimi\Academics\Support\SubjectClassAssignmentBackfiller;
use Illuminate\Console\Command;

class BackfillSubjectClassAssignmentsCommand extends Command
{
    protected $signature = 'academics:backfill-subject-class-assignments {--organization=}';

    protected $description = 'Backfill subject-class assignments from existing academic records.';

    public function handle(SubjectClassAssignmentBackfiller $backfiller): int
    {
        $count = $backfiller->run($this->option('organization'));

        $this->info("Processed {$count} subject-class assignment record(s).");

        return self::SUCCESS;
    }
}
