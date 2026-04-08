<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('illimi_subjects') || ! Schema::hasTable('illimi_syllabi')) {
            return;
        }

        if (Schema::hasColumn('illimi_subjects', 'syllabus')) {
            $subjects = DB::table('illimi_subjects')
                ->select(['id', 'organization_id', 'name', 'description', 'syllabus'])
                ->whereNull('deleted_at')
                ->whereNotNull('syllabus')
                ->where('syllabus', '!=', '')
                ->get();

            $existingSubjectIds = DB::table('illimi_syllabi')
                ->whereIn('subject_id', $subjects->pluck('id')->all())
                ->pluck('subject_id')
                ->all();

            $now = now();
            $payload = $subjects
                ->reject(fn ($subject) => in_array($subject->id, $existingSubjectIds, true))
                ->map(fn ($subject) => [
                    'id' => (string) Str::uuid(),
                    'organization_id' => $subject->organization_id,
                    'subject_id' => $subject->id,
                    'title' => trim(($subject->name ?? 'Subject').' Syllabus'),
                    'description' => $subject->description,
                    'content' => $subject->syllabus,
                    'objectives' => null,
                    'topics' => null,
                    'is_published' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
                ->values()
                ->all();

            if ($payload !== []) {
                DB::table('illimi_syllabi')->insert($payload);
            }

            Schema::table('illimi_subjects', function (Blueprint $table) {
                $table->dropColumn('syllabus');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('illimi_subjects')) {
            return;
        }

        if (! Schema::hasColumn('illimi_subjects', 'syllabus')) {
            Schema::table('illimi_subjects', function (Blueprint $table) {
                $table->longText('syllabus')->nullable()->after('description');
            });
        }
    }
};
