<?php

use Illimi\Academics\Support\SubjectClassAssignmentBackfiller;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        app(SubjectClassAssignmentBackfiller::class)->run();
    }

    public function down(): void
    {
        // Intentionally left empty: this migration backfills existing relationship data.
    }
};
