<?php

namespace Illimi\Academics\Services;

use Illimi\Academics\Events\ResultsDrafted;
use Illimi\Academics\Events\ResultsPublished;
use Illimi\Academics\Exceptions\ResultAlreadyPublishedException;
use Illimi\Academics\Exceptions\ResultPublicationBlockedException;
use Illimi\Academics\Models\AcademicClass;
use Illimi\Academics\Models\AcademicTerm;
use Illimi\Academics\Models\AcademicYear;
use Illimi\Academics\Models\GradeScale;
use Illimi\Academics\Models\Result;
use Illimi\Gradebook\Models\Report;
use Illimi\Gradebook\Models\Token;
use Illimi\Gradebook\Services\TokenService;
use Illimi\Students\Models\Student;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

class ResultService
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Result::query();

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['academic_session'])) {
            $query->where('academic_session', $filters['academic_session']);
        }

        if (!empty($filters['term'])) {
            $query->where('term', $filters['term']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function findById(string $id): ?Result
    {
        return Result::find($id);
    }

    public function draft(array $data): Result
    {
        $result = Result::updateOrCreate([
            'student_id' => $data['student_id'],
            'class_id' => $data['class_id'],
            'academic_session' => $data['academic_session'],
            'term' => $data['term'],
        ], $data);

        Event::dispatch(new ResultsDrafted($result));

        return $result->fresh();
    }

    public function publish(array $resultIds, string $publishedBy): int
    {
        $results = Result::query()->whereIn('id', $resultIds)->get();
        $this->assertResultsPublishable($results);

        $publishedIds = [];

        foreach ($results as $result) {
            $status = $this->resultStatusValue($result->status);

            if ($status === 'published') {
                throw new ResultAlreadyPublishedException($result->id);
            }

            if ($status === 'archived') {
                continue;
            }

            $result->update([
                'status' => 'published',
                'published_by' => $publishedBy,
                'published_at' => now(),
            ]);

            $this->syncPublishedReportToken($result);

            $publishedIds[] = $result->id;
        }

        if (!empty($publishedIds)) {
            Event::dispatch(new ResultsPublished($publishedIds, $publishedBy));
        }

        return count($publishedIds);
    }

    public function unpublish(array $resultIds): int
    {
        $results = Result::query()->whereIn('id', $resultIds)->get();
        $unpublishedIds = [];

        foreach ($results as $result) {
            if ($this->resultStatusValue($result->status) !== 'published') {
                continue;
            }

            $result->update([
                'status' => 'under_review',
                'published_by' => null,
                'published_at' => null,
            ]);

            $unpublishedIds[] = $result->id;
        }

        return count($unpublishedIds);
    }

    public function resultIdsForScope(string $classId, string $academicSession, string $term): array
    {
        return Result::query()
            ->where('class_id', $classId)
            ->where('academic_session', $academicSession)
            ->where('term', $term)
            ->pluck('id')
            ->all();
    }

    public function publicationScopes(?string $organizationId, ?string $academicYearId, ?string $academicTermId): Collection
    {
        $classes = AcademicClass::query()
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->with('section:id,name')
            ->withCount([
                'subjects',
                'students as active_students_count' => fn ($query) => $query->where('status', Student::STATUS_ACTIVE),
            ])
            ->orderBy('name')
            ->get(['id', 'organization_id', 'name', 'level']);

        if (! $academicYearId || ! $academicTermId) {
            return $classes->map(function (AcademicClass $class) {
                return [
                    'class_id' => $class->id,
                    'class_name' => $this->classDisplayName($class),
                    'class_section_name' => $class->section?->name,
                    'level' => $class->level,
                    'student_count' => (int) ($class->active_students_count ?? 0),
                    'subject_count' => (int) ($class->subjects_count ?? 0),
                    'ready_students_count' => 0,
                    'published_students_count' => 0,
                    'can_publish' => false,
                ];
            });
        }

        $academicYear = AcademicYear::query()->find($academicYearId);
        $academicTerm = AcademicTerm::query()->find($academicTermId);

        if (! $academicYear || ! $academicTerm) {
            return collect();
        }

        // Performance: We no longer sync results for every class on the index page.
        // This process is expensive for large data volumes. Synchronization now
        // happens when a class is viewed for publication or during a background task.

        $assessmentCounts = DB::table('illimi_gradebook_assessments')
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->where('academic_year_id', $academicYearId)
            ->where('academic_term_id', $academicTermId)
            ->whereNull('deleted_at')
            ->select('academic_class_id', 'student_id', DB::raw('COUNT(DISTINCT subject_id) as recorded_subjects'))
            ->groupBy('academic_class_id', 'student_id')
            ->get()
            ->groupBy('academic_class_id');

        $resultCounts = Result::query()
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->where('academic_session', $academicYear->name)
            ->where('term', $academicTerm->name)
            ->select(
                'class_id',
                DB::raw('COUNT(*) as total_results'),
                DB::raw("SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published_results")
            )
            ->groupBy('class_id')
            ->get()
            ->keyBy('class_id');

        return $classes->map(function (AcademicClass $class) use ($assessmentCounts, $resultCounts) {
            $studentCount = (int) ($class->active_students_count ?? 0);
            $subjectCount = (int) ($class->subjects_count ?? 0);
            $classAssessmentRows = collect($assessmentCounts->get($class->id, []));
            $resultRow = $resultCounts->get($class->id);

            $readyStudentsCount = $subjectCount > 0
                ? $classAssessmentRows
                    ->filter(fn ($row) => (int) $row->recorded_subjects >= $subjectCount)
                    ->count()
                : 0;

            $publishedStudentsCount = (int) ($resultRow->published_results ?? 0);
            $resultCount = (int) ($resultRow->total_results ?? 0);

            return [
                'class_id' => $class->id,
                'class_name' => $this->classDisplayName($class),
                'class_section_name' => $class->section?->name,
                'level' => $class->level,
                'student_count' => $studentCount,
                'subject_count' => $subjectCount,
                'ready_students_count' => $readyStudentsCount,
                'result_count' => $resultCount,
                'published_students_count' => $publishedStudentsCount,
                'can_publish' => $studentCount > 0
                    && $subjectCount > 0
                    && $readyStudentsCount >= $studentCount
                    && $resultCount >= $studentCount,
            ];
        });
    }

    public function publicationPreview(
        string $classId,
        string $academicYearId,
        string $academicTermId,
        ?string $organizationId = null
    ): array {
        [$class, $academicYear, $academicTerm] = $this->resolvePublicationContext(
            $classId,
            $academicYearId,
            $academicTermId,
            $organizationId
        );

        $rows = $this->buildPublicationRows($class, $academicYear, $academicTerm, $organizationId);

        return [
            'class' => [
                'id' => $class->id,
                'name' => $this->classDisplayName($class),
                'base_name' => $class->name,
                'section_name' => $class->section?->name,
                'level' => $class->level,
            ],
            'academic_year' => [
                'id' => $academicYear->id,
                'name' => $academicYear->name,
            ],
            'academic_term' => [
                'id' => $academicTerm->id,
                'name' => $academicTerm->name,
            ],
            'summary' => [
                'student_count' => $rows->count(),
                'subject_count' => (int) $class->subjects->count(),
                'published_count' => $rows->where('status', 'published')->count(),
                'ready_count' => $rows->where('all_subjects_recorded', true)->count(),
            ],
            'students' => $rows->values()->all(),
        ];
    }

    public function publicResultSlip(?string $admissionNumber = null, ?string $tokenCode = null, ?string $organizationId = null): ?array
    {
        $normalizedToken = strtoupper(trim((string) $tokenCode));
        $normalizedAdmissionNumber = trim((string) $admissionNumber);

        if ($normalizedToken === '') {
            return null;
        }

        $report = Report::query()
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->where('code', $normalizedToken)
            ->latest()
            ->first();

        if (! $report || ! $report->code) {
            return null;
        }

        $student = Student::query()
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->find($report->student_id);

        if (! $student) {
            return null;
        }

        if ($normalizedAdmissionNumber !== '' && $student->admission_number !== $normalizedAdmissionNumber) {
            return null;
        }

        $academicYear = AcademicYear::query()
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->find($report->academic_year_id);

        $academicTerm = AcademicTerm::query()
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->find($report->academic_term_id);

        if (! $academicYear || ! $academicTerm) {
            return null;
        }

        $result = Result::query()
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->with(['academicClass.section'])
            ->where('student_id', $student->id)
            ->where('class_id', $report->academic_class_id)
            ->where('academic_session', $academicYear->name)
            ->where('term', $academicTerm->name)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->orderByDesc('updated_at')
            ->latest()
            ->first();

        if (! $result) {
            return null;
        }

        $token = Token::query()
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->where('student_id', $student->id)
            ->where('academic_class_id', $result->class_id)
            ->where('academic_year_id', $academicYear->id)
            ->where('academic_term_id', $academicTerm->id)
            ->where('code', $report->code)
            ->first();

        if (! $token || ! $token->is_active) {
            return null;
        }

        if ($token) {
            app(TokenService::class)->markAsUsed($token);
        }

        $publicationData = $this->publicationPreview(
            $result->class_id,
            $academicYear->id,
            $academicTerm->id,
            $organizationId
        );

        $studentRow = collect($publicationData['students'] ?? [])->firstWhere('student_id', $student->id);
        $classPublishedCount = Result::query()
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->where('class_id', $result->class_id)
            ->where('academic_session', $result->academic_session)
            ->where('term', $result->term)
            ->where('status', 'published')
            ->count();
        $studentRating = DB::table('illimi_gradebook_student_ratings')
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->where('student_id', $student->id)
            ->where('academic_class_id', $result->class_id)
            ->where('academic_year_id', $academicYear->id)
            ->where('academic_term_id', $academicTerm->id)
            ->first();

        return [
            'student' => [
                'id' => $student->id,
                'full_name' => trim($student->full_name),
                'admission_number' => $student->admission_number,
            ],
            'class' => $publicationData['class'] ?? [
                'id' => $result->academicClass?->id,
                'name' => $result->academicClass ? $this->classDisplayName($result->academicClass) : null,
            ],
            'academic_year' => [
                'id' => $academicYear->id,
                'name' => $academicYear->name,
            ],
            'academic_term' => [
                'id' => $academicTerm->id,
                'name' => $academicTerm->name,
            ],
            'summary' => [
                'position' => $result->position_in_class,
                'out_of' => $classPublishedCount,
                'subject_count' => count($studentRow['assessments'] ?? []),
                'overall_total' => (float) ($studentRow['total_score'] ?? $result->total_score ?? 0),
                'average_score' => (float) ($studentRow['average_score'] ?? 0),
                'grade' => $result->grade,
                'remark' => $result->remark,
            ],
            'assessments' => $studentRow['assessments'] ?? [],
            'student_ratings' => [
                'effective_assessment' => $this->normalizeStudentRatingEntries(
                    json_decode($studentRating?->effective_assessment ?? '[]', true) ?: [],
                    [
                        'attentiveness' => 'Attentiveness',
                        'conduct' => 'Conduct',
                        'neatness' => 'Neatness',
                        'politeness' => 'Politeness',
                        'punctuality' => 'Punctuality',
                        'relationship' => 'Relationship',
                    ]
                ),
                'psychomotor_assessment' => $this->normalizeStudentRatingEntries(
                    json_decode($studentRating?->psychomotor_assessment ?? '[]', true) ?: [],
                    [
                        'assignment' => 'Assignment',
                        'construction' => 'Construction',
                        'fluency' => 'Fluency',
                        'hand_writing' => 'Hand Writing',
                        'sport_and_games' => 'Sport and Games',
                    ]
                ),
            ],
            'published_result' => [
                'id' => $result->id,
                'published_at' => $result->published_at?->toIso8601String(),
                'token' => $report->code,
                'report_id' => $report->id,
            ],
        ];
    }

    protected function syncPublishedReportToken(Result $result): void
    {
        $academicYear = $this->resolveAcademicYearForSession($result->organization_id, $result->academic_session);
        $academicTerm = $academicYear
            ? $this->resolveAcademicTermForScope($result->organization_id, $result->term, $academicYear->id)
            : null;

        if (! $academicYear || ! $academicTerm) {
            return;
        }

        app(TokenService::class)->ensureForScope([
            'organization_id' => $result->organization_id,
            'student_id' => $result->student_id,
            'academic_class_id' => $result->class_id,
            'academic_year_id' => $academicYear->id,
            'academic_term_id' => $academicTerm->id,
            'generated_by' => $result->published_by,
            'is_active' => true,
        ]);
    }

    protected function assertResultsPublishable(EloquentCollection $results): void
    {
        if ($results->isEmpty()) {
            throw new ResultPublicationBlockedException('No results were found for publication.');
        }

        $results
            ->groupBy(fn (Result $result) => implode('|', [
                $result->class_id,
                $result->academic_session,
                $result->term,
            ]))
            ->each(function (EloquentCollection $scopeResults): void {
                $this->assertScopePublishable($scopeResults);
            });
    }

    protected function assertScopePublishable(EloquentCollection $scopeResults): void
    {
        /** @var Result $reference */
        $reference = $scopeResults->first();
        $class = AcademicClass::query()
            ->with('subjects:id,name')
            ->find($reference->class_id);

        if (! $class) {
            throw new ResultPublicationBlockedException('The selected class for this publication batch could not be found.');
        }

        $subjects = $class->subjects;

        if ($subjects->isEmpty()) {
            throw new ResultPublicationBlockedException(sprintf(
                'Results for %s cannot be published because no subjects are assigned to the class.',
                $class->name
            ));
        }

        $academicYear = $this->resolveAcademicYearForSession($reference->organization_id, $reference->academic_session);

        if (! $academicYear) {
            throw new ResultPublicationBlockedException(sprintf(
                'Results for %s cannot be published because the academic session "%s" could not be matched to an academic year.',
                $class->name,
                $reference->academic_session
            ));
        }

        $academicTerm = $this->resolveAcademicTermForScope(
            $reference->organization_id,
            $reference->term,
            $academicYear->id
        );

        if (! $academicTerm) {
            throw new ResultPublicationBlockedException(sprintf(
                'Results for %s cannot be published because the term "%s" could not be matched to an academic term in %s.',
                $class->name,
                $reference->term,
                $academicYear->name
            ));
        }

        $students = Student::query()
            ->where('class_id', $class->id)
            ->where('status', Student::STATUS_ACTIVE)
            ->get(['id', 'first_name', 'last_name']);

        if ($students->isEmpty()) {
            throw new ResultPublicationBlockedException(sprintf(
                'Results for %s cannot be published because there are no active students in the class.',
                $class->name
            ));
        }

        $selectedStudentIds = $scopeResults->pluck('student_id')->unique()->values();
        $classStudentIds = $students->pluck('id')->unique()->values();
        $missingResultStudentIds = $classStudentIds->diff($selectedStudentIds)->values();

        if ($missingResultStudentIds->isNotEmpty()) {
            $missingStudents = $students
                ->whereIn('id', $missingResultStudentIds)
                ->map(fn (Student $student) => trim($student->full_name))
                ->filter()
                ->values()
                ->all();

            throw new ResultPublicationBlockedException(sprintf(
                'Results for %s cannot be published because result records are still missing for: %s.',
                $class->name,
                implode(', ', array_slice($missingStudents, 0, 10))
            ));
        }

        $subjectIds = $subjects->pluck('id')->values();
        $assessmentPairs = DB::table('illimi_gradebook_assessments')
            ->where('academic_class_id', $class->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('academic_term_id', $academicTerm->id)
            ->whereIn('student_id', $classStudentIds)
            ->whereIn('subject_id', $subjectIds)
            ->select('student_id', 'subject_id')
            ->distinct()
            ->get()
            ->map(fn ($row) => $row->student_id.'|'.$row->subject_id);

        $expectedPairs = $classStudentIds->flatMap(function (string $studentId) use ($subjectIds): Collection {
            return $subjectIds->map(fn (string $subjectId) => $studentId.'|'.$subjectId);
        });

        $missingPairs = $expectedPairs->diff($assessmentPairs)->values();

        if ($missingPairs->isNotEmpty()) {
            $subjectNames = $subjects->pluck('name', 'id');
            $studentNames = $students->mapWithKeys(fn (Student $student) => [$student->id => trim($student->full_name)]);
            $examples = $missingPairs
                ->take(8)
                ->map(function (string $pair) use ($studentNames, $subjectNames): string {
                    [$studentId, $subjectId] = explode('|', $pair);

                    return sprintf(
                        '%s - %s',
                        $studentNames[$studentId] ?? 'Unknown student',
                        $subjectNames[$subjectId] ?? 'Unknown subject'
                    );
                })
                ->all();

            throw new ResultPublicationBlockedException(sprintf(
                'Results for %s cannot be published until every subject assessment has been recorded. Missing entries include: %s.',
                $class->name,
                implode(', ', $examples)
            ));
        }
    }

    protected function resolveAcademicYearForSession(?string $organizationId, string $academicSession): ?AcademicYear
    {
        $normalized = $this->normalizeLabel($academicSession);

        return AcademicYear::query()
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->get()
            ->first(function (AcademicYear $year) use ($normalized): bool {
                return $this->normalizeLabel($year->name) === $normalized
                    || $this->normalizeLabel((string) $year->slug) === $normalized;
            });
    }

    protected function resolveAcademicTermForScope(?string $organizationId, string $term, string $academicYearId): ?AcademicTerm
    {
        $normalized = $this->normalizeLabel($term);

        return AcademicTerm::query()
            ->where('academic_year_id', $academicYearId)
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->get()
            ->first(function (AcademicTerm $academicTerm) use ($normalized): bool {
                return $this->normalizeLabel($academicTerm->name) === $normalized
                    || $this->normalizeLabel((string) $academicTerm->slug) === $normalized;
            });
    }

    protected function normalizeLabel(?string $value): string
    {
        return Str::of((string) $value)
            ->lower()
            ->replace(['_', '-', '.', '/'], ' ')
            ->squish()
            ->value();
    }

    protected function resolvePublicationContext(
        string $classId,
        string $academicYearId,
        string $academicTermId,
        ?string $organizationId = null
    ): array {
        $class = AcademicClass::query()
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->with(['subjects:id,name', 'section:id,name'])
            ->findOrFail($classId);

        $academicYear = AcademicYear::query()
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->findOrFail($academicYearId);

        $academicTerm = AcademicTerm::query()
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->where('academic_year_id', $academicYear->id)
            ->findOrFail($academicTermId);

        return [$class, $academicYear, $academicTerm];
    }

    protected function buildPublicationRows(
        AcademicClass $class,
        AcademicYear $academicYear,
        AcademicTerm $academicTerm,
        ?string $organizationId = null
    ): Collection {
        $students = Student::query()
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->where('class_id', $class->id)
            ->where('status', Student::STATUS_ACTIVE)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'admission_number']);

        $assessments = DB::table('illimi_gradebook_assessments as assessments')
            ->leftJoin('illimi_subjects as subjects', 'subjects.id', '=', 'assessments.subject_id')
            ->leftJoin('illimi_grade_scales as grade_scales', 'grade_scales.id', '=', 'assessments.grade_scale_id')
            ->when($organizationId, fn ($query) => $query->where('assessments.organization_id', $organizationId))
            ->where('assessments.academic_class_id', $class->id)
            ->where('assessments.academic_year_id', $academicYear->id)
            ->where('assessments.academic_term_id', $academicTerm->id)
            ->whereIn('assessments.student_id', $students->pluck('id'))
            ->whereNull('assessments.deleted_at')
            ->orderBy('subjects.name')
            ->get([
                'assessments.id as assessment_id',
                'assessments.student_id',
                'assessments.subject_id',
                'subjects.name as subject_name',
            ]);

        $gradeScales = GradeScale::query()
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->orderByDesc('min_score')
            ->get();

        $assessmentItemsByAssessmentId = $this->getAssessmentItemsByAssessmentId($assessments);
        $assessmentTotalsByAssessmentId = $this->getAssessmentTotals($assessments, $assessmentItemsByAssessmentId);

        $this->syncResultsFromAssessments(
            $class,
            $academicYear,
            $academicTerm,
            $students,
            $assessments,
            $assessmentTotalsByAssessmentId,
            $gradeScales,
            $organizationId
        );

        $results = Result::query()
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->where('class_id', $class->id)
            ->where('academic_session', $academicYear->name)
            ->where('term', $academicTerm->name)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        // Totals already calculated above using getAssessmentTotals

        $subjectParticipantCounts = $assessments
            ->groupBy('subject_id')
            ->map(fn (Collection $subjectAssessments) => $subjectAssessments->pluck('student_id')->unique()->count());

        $subjectRankings = $assessments
            ->groupBy('subject_id')
            ->map(function (Collection $subjectAssessments) use ($assessmentTotalsByAssessmentId) {
                $sorted = $subjectAssessments
                    ->sortBy([
                        fn ($assessmentRow) => -1 * (float) ($assessmentTotalsByAssessmentId->get($assessmentRow->assessment_id) ?? 0),
                        fn ($assessmentRow) => (string) ($assessmentRow->student_id ?? ''),
                    ])
                    ->values();

                $rank = 0;
                $position = 0;
                $previousScore = null;
                $ranks = [];

                foreach ($sorted as $assessmentRow) {
                    $position++;
                    $score = (float) ($assessmentTotalsByAssessmentId->get($assessmentRow->assessment_id) ?? 0);

                    if ($previousScore === null || abs($score - $previousScore) > 0.00001) {
                        $rank = $position;
                        $previousScore = $score;
                    }

                    $ranks[$assessmentRow->assessment_id] = $rank;
                }

                return $ranks;
            });

        $assessmentsByStudent = $assessments->groupBy('student_id');

        $rows = $students->map(function (Student $student) use ($assessmentsByStudent, $results, $class, $assessmentItemsByAssessmentId, $assessmentTotalsByAssessmentId, $subjectRankings, $subjectParticipantCounts, $gradeScales) {
            $studentAssessments = collect($assessmentsByStudent->get($student->id, []));
            $subjectCount = $studentAssessments->pluck('subject_id')->unique()->count();
            $assessmentCount = $studentAssessments->count();
            $assessmentItems = $studentAssessments->map(function ($assessmentRow) use ($assessmentItemsByAssessmentId, $gradeScales) {
                $itemRows = collect($assessmentItemsByAssessmentId->get($assessmentRow->assessment_id, []));

                if ($itemRows->isNotEmpty()) {
                    $components = $itemRows->map(function ($itemRow) {
                        return [
                            'label' => $itemRow->label ?: ($itemRow->code ?: 'Component'),
                            'code' => $itemRow->code,
                            'component_type' => $itemRow->component_type,
                            'affects_total' => (bool) ($itemRow->affects_total ?? true),
                            'score' => round((float) $itemRow->score, 2),
                        ];
                    })->values();

                    $totalScore = $components
                        ->filter(fn (array $component) => $component['affects_total'])
                        ->sum('score');
                } else {
                    $components = collect([]);
                    $totalScore = 0.0;
                }

                $gradeScale = $this->calculateGradeFromScore($totalScore, $gradeScales);

                return [
                    'subject_id' => $assessmentRow->subject_id,
                    'subject_name' => $assessmentRow->subject_name,
                    'components' => $components->all(),
                    'total_score' => round((float) $totalScore, 2),
                    'grade' => $gradeScale?->code ?? 'F',
                    'remark' => $gradeScale?->description,
                ];
            })->values();

            $assessmentItems = $assessmentItems->map(function (array $assessment) use ($subjectRankings, $subjectParticipantCounts, $assessmentTotalsByAssessmentId, $studentAssessments) {
                $assessmentRow = $studentAssessments->firstWhere('subject_id', $assessment['subject_id']);
                $assessmentId = $assessmentRow?->assessment_id;

                $assessment['total_score'] = round((float) ($assessmentTotalsByAssessmentId->get($assessmentId) ?? $assessment['total_score']), 2);
                $assessment['subject_rank'] = $assessmentId ? ($subjectRankings->get($assessment['subject_id'])[$assessmentId] ?? null) : null;
                $assessment['subject_participant_count'] = $subjectParticipantCounts->get($assessment['subject_id']) ?? null;

                return $assessment;
            })->values();

            $total = round((float) ($studentAssessments->sum(fn ($assessmentRow) => $assessmentTotalsByAssessmentId->get($assessmentRow->assessment_id) ?? 0)), 2);
            $average = $assessmentCount > 0 ? round($total / $assessmentCount, 2) : 0.0;

            /** @var Result|null $result */
            $result = $results->get($student->id);
            $status = $this->resultStatusValue($result?->status) ?? 'missing';

            return [
                'student_id' => $student->id,
                'student_name' => trim($student->full_name),
                'admission_number' => $student->admission_number,
                'result_id' => $result?->id,
                'status' => $status,
                'subjects_recorded' => $subjectCount,
                'subject_count' => (int) $class->subjects->count(),
                'all_subjects_recorded' => $subjectCount >= (int) $class->subjects->count() && $class->subjects->count() > 0,
                'assignment1' => 0,
                'assignment2' => 0,
                'test1' => 0,
                'test2' => 0,
                'exams' => 0,
                'total_score' => $total,
                'average_score' => $average,
                'grade' => $result?->grade,
                'remark' => $result?->remark,
                'assessments' => $assessmentItems->all(),
            ];
        })->sortBy([
            ['total_score', 'desc'],
            ['average_score', 'desc'],
            ['student_name', 'asc'],
        ])->values();

        return $rows->values()->map(function (array $row, int $index) {
            $row['rank'] = $index + 1;

            return $row;
        });
    }

    protected function resultStatusValue(mixed $status): ?string
    {
        if ($status instanceof \BackedEnum) {
            return $status->value;
        }

        return $status ?: null;
    }

    protected function classDisplayName(AcademicClass $class): string
    {
        $sectionName = trim((string) ($class->section?->name ?? ''));

        return $sectionName !== ''
            ? sprintf('%s - %s', $class->name, $sectionName)
            : $class->name;
    }

    protected function syncResultsFromAssessments(
        AcademicClass $class,
        AcademicYear $academicYear,
        AcademicTerm $academicTerm,
        Collection $students,
        Collection $assessments,
        Collection $assessmentTotals,
        Collection $gradeScales,
        ?string $organizationId = null
    ): void {
        if ($students->isEmpty() || $assessments->isEmpty()) {
            return;
        }

        // Grade scales are now passed as an argument

        $studentSummaries = $assessments
            ->groupBy('student_id')
            ->map(function (Collection $studentAssessments, string $studentId) use ($gradeScales, $assessmentTotals) {
                $overallTotal = (float) $studentAssessments->sum(function ($assessment) use ($assessmentTotals) {
                    return (float) ($assessmentTotals->get($assessment->assessment_id) ?? 0);
                });
                $subjectCount = $studentAssessments->pluck('subject_id')->unique()->count();
                $averageScore = $subjectCount > 0 ? round($overallTotal / $subjectCount, 2) : 0.0;
                $gradeScale = $this->calculateGradeFromScore($averageScore, $gradeScales);

                return [
                    'student_id' => $studentId,
                    'overall_total' => round($overallTotal, 2),
                    'average_score' => $averageScore,
                    'grade' => $gradeScale?->code,
                    'remark' => $gradeScale?->description,
                ];
            })
            ->sortBy([
                ['overall_total', 'desc'],
                ['student_id', 'asc'],
            ])
            ->values();

        $position = 0;
        $rank = 0;
        $previousTotal = null;
        $rankings = [];

        foreach ($studentSummaries as $summary) {
            $position++;

            if ($previousTotal === null || abs((float) $summary['overall_total'] - (float) $previousTotal) > 0.00001) {
                $rank = $position;
                $previousTotal = (float) $summary['overall_total'];
            }

            $rankings[$summary['student_id']] = $rank;
        }

        $existingResults = Result::query()
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->where('class_id', $class->id)
            ->where('academic_session', $academicYear->name)
            ->where('term', $academicTerm->name)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        foreach ($studentSummaries as $summary) {
            /** @var Result|null $existing */
            $existing = $existingResults->get($summary['student_id']);

            $data = [
                'organization_id' => $organizationId,
                'total_score' => $summary['overall_total'],
                'grade' => $summary['grade'],
                'remark' => $summary['remark'],
                'position_in_class' => $rankings[$summary['student_id']] ?? null,
                'status' => $this->resultStatusValue($existing?->status) ?? 'under_review',
                'published_by' => $existing?->published_by,
                'published_at' => $existing?->published_at,
            ];

            // Performance: Only update if the record doesn't exist or if values have changed
            if (! $existing) {
                Result::query()->create(array_merge([
                    'student_id' => $summary['student_id'],
                    'class_id' => $class->id,
                    'academic_session' => $academicYear->name,
                    'term' => $academicTerm->name,
                ], $data));
            } else {
                $needsUpdate = false;
                foreach ($data as $key => $value) {
                    if ($key === 'status') {
                        $currentStatus = $this->resultStatusValue($existing->status);
                        if ($currentStatus !== $value) {
                            $needsUpdate = true;
                            break;
                        }
                        continue;
                    }

                    if ($existing->{$key} != $value) {
                        $needsUpdate = true;
                        break;
                    }
                }

                if ($needsUpdate) {
                    $existing->update($data);
                }
            }
        }
    }

    protected function normalizeStudentRatingEntries(array $values, array $labels): array
    {
        return collect($labels)->map(function (string $label, string $key) use ($values) {
            $value = $values[$key] ?? null;

            return [
                'key' => $key,
                'label' => $label,
                'value' => $value,
                'rating' => match ((int) $value) {
                    5 => 'A',
                    4 => 'B',
                    3 => 'C',
                    2 => 'D',
                    1 => 'E',
                    default => null,
                },
            ];
        })->values()->all();
    }

    protected function getAssessmentItemsByAssessmentId(Collection $assessments): Collection
    {
        return DB::table('illimi_gradebook_assessment_items as items')
            ->leftJoin('illimi_gradebook_template_items as template_items', 'template_items.id', '=', 'items.template_item_id')
            ->whereIn('items.assessment_id', $assessments->pluck('assessment_id')->filter()->values())
            ->whereNull('items.deleted_at')
            ->orderBy('template_items.position')
            ->get([
                'items.assessment_id',
                'items.score',
                'template_items.label',
                'template_items.code',
                'template_items.component_type',
                'template_items.affects_total',
            ])
            ->groupBy('assessment_id');
    }

    protected function getAssessmentTotals(Collection $assessments, Collection $assessmentItemsByAssessmentId): Collection
    {
        return $assessments->mapWithKeys(function ($assessmentRow) use ($assessmentItemsByAssessmentId) {
            $itemRows = collect($assessmentItemsByAssessmentId->get($assessmentRow->assessment_id, []));

            $totalScore = $itemRows
                ->filter(fn ($itemRow) => (bool) ($itemRow->affects_total ?? true))
                ->sum(fn ($itemRow) => (float) $itemRow->score);

            return [$assessmentRow->assessment_id => round((float) $totalScore, 2)];
        });
    }

    protected function calculateGradeFromScore(float $score, Collection $gradeScales): ?GradeScale
    {
        return $gradeScales->first(function (GradeScale $gradeScale) use ($score): bool {
            $minScore = $gradeScale->min_score !== null ? (float) $gradeScale->min_score : null;
            $maxScore = $gradeScale->max_score !== null ? (float) $gradeScale->max_score : null;

            if ($minScore === null && $maxScore === null) {
                return false;
            }

            return ($minScore === null || $score >= $minScore)
                && ($maxScore === null || $score <= $maxScore);
        });
    }
}
