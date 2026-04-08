<?php

namespace Illimi\Academics\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SubjectClassAssignmentBackfiller
{
    public function run(?string $organizationId = null): int
    {
        if (! Schema::hasTable('illimi_class_subject')) {
            return 0;
        }

        $records = $this->records($organizationId);

        if ($records->isEmpty()) {
            return 0;
        }

        $now = now();
        $payload = $records
            ->map(fn (object $record) => [
                'organization_id' => $record->organization_id,
                'class_id' => $record->class_id,
                'subject_id' => $record->subject_id,
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->values();

        foreach ($payload->chunk(250) as $chunk) {
            DB::table('illimi_class_subject')->insertOrIgnore($chunk->all());
        }

        return $payload->count();
    }

    protected function records(?string $organizationId = null): Collection
    {
        $questionBanks = DB::table('illimi_question_banks')
            ->select(['organization_id', 'class_id', 'subject_id'])
            ->whereNull('deleted_at')
            ->whereNotNull('class_id')
            ->whereNotNull('subject_id');

        $exams = DB::table('illimi_exams')
            ->select(['organization_id', 'class_id', 'subject_id'])
            ->whereNull('deleted_at');

        $gradebookEntries = DB::table('illimi_gradebook_entries')
            ->select(['organization_id', 'class_id', 'subject_id'])
            ->whereNull('deleted_at');

        foreach ([$questionBanks, $exams, $gradebookEntries] as $query) {
            if ($organizationId) {
                $query->where('organization_id', $organizationId);
            }
        }

        return $questionBanks
            ->union($exams)
            ->union($gradebookEntries)
            ->get()
            ->filter(fn (object $record) => filled($record->organization_id) && filled($record->class_id) && filled($record->subject_id))
            ->unique(fn (object $record) => implode('|', [$record->organization_id, $record->class_id, $record->subject_id]))
            ->values();
    }
}
