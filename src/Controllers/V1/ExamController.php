<?php

namespace Illimi\Academics\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Codizium\Core\Helpers\CoreJsonResponse;
use Illimi\Academics\Events\AcademicEntityChanged;
use Illimi\Academics\Models\Exam;
use Illimi\Academics\Requests\StartExamRequest;
use Illimi\Academics\Requests\StoreExamRequest;
use Illimi\Academics\Requests\UpdateExamRequest;
use Illimi\Academics\Requests\SubmitExamAttemptRequest;
use Illimi\Academics\Resources\ExamAttemptResource;
use Illimi\Academics\Resources\ExamResource;
use Illimi\Academics\Services\ExamService;
use Illuminate\Http\Request;

class ExamController extends BaseController
{
    public function __construct(
        protected ExamService $service,
        protected CoreJsonResponse $response
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/exams",
     *     summary="List exams",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Exams retrieved")
     * )
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = array_filter([
            'class_id' => $request->query('class_id'),
            'subject_id' => $request->query('subject_id'),
            'academic_session' => $request->query('academic_session'),
            'term' => $request->query('term'),
            'status' => $request->query('status'),
        ], fn ($value) => $value !== null && $value !== '');

        $exams = $this->service->list($filters, $perPage);

        return $this->response->success(ExamResource::collection($exams), 'Exams retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/academics/exams",
     *     summary="Create exam",
     *     tags={"Academics"},
     *     @OA\Response(response=201, description="Exam created")
     * )
     */
    public function store(StoreExamRequest $request)
    {
        // $this->authorize('create', Exam::class);

        $exam = $this->service->create($request->validated());
        $exam = $this->service->findById($exam->id) ?? $exam;

        event(new AcademicEntityChanged('exam', 'created', $this->examPayload($exam)));

        return $this->response->success(new ExamResource($exam), 'Exam created successfully', 201);
    }

    public function show(string $id)
    {
        $exam = $this->service->findById($id);

        if (! $exam) {
            return $this->response->error('Exam not found', 404);
        }

        return $this->response->success(new ExamResource($exam), 'Exam retrieved successfully');
    }

    public function update(UpdateExamRequest $request, string $id)
    {
        $existing = $this->service->findById($id);
        $exam = $this->service->update($id, $request->validated());

        if (! $exam) {
            return $this->response->error('Exam not found', 404);
        }

        event(new AcademicEntityChanged('exam', 'updated', array_merge(
            $this->examPayload($exam),
            ['previous_status' => $existing?->status?->value ?? $existing?->status]
        )));

        return $this->response->success(new ExamResource($exam), 'Exam updated successfully');
    }

    public function destroy(string $id)
    {
        $exam = $this->service->findById($id);
        $deleted = $this->service->delete($id);

        if (! $deleted) {
            return $this->response->error('Exam not found', 404);
        }

        if ($exam) {
            event(new AcademicEntityChanged('exam', 'deleted', $this->examPayload($exam)));
        }

        return $this->response->success([], 'Exam deleted successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/academics/exams/{id}/start",
     *     summary="Start exam",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Exam started")
     * )
     */
    public function start(StartExamRequest $request, string $id)
    {
        $payload = $request->validated();

        $attempt = $this->service->startExam($id, $payload['student_id'], [
            'ip_address' => $payload['ip_address'] ?? null,
            'browser_info' => $payload['browser_info'] ?? null,
        ]);

        return $this->response->success(new ExamAttemptResource($attempt), 'Exam started successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/academics/exams/{id}/submit",
     *     summary="Submit exam",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Exam submitted")
     * )
     */
    public function submit(SubmitExamAttemptRequest $request, string $id)
    {
        $payload = $request->validated();

        $attempt = $this->service->submitExam($id, $payload['student_id'], $payload['answers']);

        return $this->response->success(new ExamAttemptResource($attempt), 'Exam submitted successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/exams/{id}/results",
     *     summary="Exam results",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Exam results retrieved")
     * )
     */
    public function results(Request $request, string $id)
    {
        $perPage = (int) $request->query('per_page', 15);
        $attempts = $this->service->getResults($id, $perPage);

        return $this->response->success(ExamAttemptResource::collection($attempts), 'Exam results retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/exams/{id}/item-analysis",
     *     summary="Item analysis",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Item analysis retrieved")
     * )
     */
    public function itemAnalysis(string $id)
    {
        $analysis = $this->service->getItemAnalysis($id);

        return $this->response->success($analysis, 'Item analysis retrieved successfully');
    }

    protected function examPayload(object $exam): array
    {
        return [
            'id' => $exam->id,
            'organization_id' => $exam->organization_id,
            'title' => $exam->title,
            'name' => $exam->title,
            'subject_id' => $exam->subject_id,
            'subject_name' => $exam->subject?->name,
            'class_id' => $exam->class_id,
            'class_name' => $exam->academicClass?->name,
            'academic_session' => $exam->academic_session,
            'term' => $exam->term,
            'duration_minutes' => $exam->duration_minutes,
            'total_marks' => $exam->total_marks !== null ? (float) $exam->total_marks : null,
            'pass_mark' => $exam->pass_mark !== null ? (float) $exam->pass_mark : null,
            'negative_marking' => (bool) $exam->negative_marking,
            'negative_mark_value' => $exam->negative_mark_value !== null ? (float) $exam->negative_mark_value : null,
            'randomise_questions' => (bool) $exam->randomise_questions,
            'randomise_options' => (bool) $exam->randomise_options,
            'allow_review' => (bool) $exam->allow_review,
            'status' => $exam->status?->value ?? $exam->status,
            'starts_at' => $exam->starts_at?->toIso8601String(),
            'ends_at' => $exam->ends_at?->toIso8601String(),
        ];
    }
}
