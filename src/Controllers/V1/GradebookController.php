<?php

namespace Illimi\Academics\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Codizium\Core\Helpers\CoreJsonResponse;
use Illimi\Academics\Exceptions\GradeWeightingException;
use Illimi\Academics\Events\AcademicEntityChanged;
use Illimi\Academics\Requests\StoreGradebookEntryRequest;
use Illimi\Academics\Requests\UpdateGradebookEntryRequest;
use Illimi\Academics\Resources\GradebookEntryResource;
use Illimi\Academics\Services\GradebookService;
use Illuminate\Http\Request;

class GradebookController extends BaseController
{
    public function __construct(
        protected GradebookService $service,
        protected CoreJsonResponse $response
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/gradebook",
     *     summary="List gradebook entries",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Gradebook entries retrieved")
     * )
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = array_filter([
            'student_id' => $request->query('student_id'),
            'class_id' => $request->query('class_id'),
            'subject_id' => $request->query('subject_id'),
            'academic_session' => $request->query('academic_session'),
            'term' => $request->query('term'),
        ], fn ($value) => $value !== null && $value !== '');

        $entries = $this->service->list($filters, $perPage);

        return $this->response->success(GradebookEntryResource::collection($entries), 'Gradebook entries retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/academics/gradebook",
     *     summary="Create or update gradebook entry",
     *     tags={"Academics"},
     *     @OA\Response(response=201, description="Gradebook entry saved")
     * )
     */
    public function store(StoreGradebookEntryRequest $request)
    {
        try {
            $entry = $this->service->store($request->validated());
        } catch (GradeWeightingException $exception) {
            return $this->response->error($exception->getMessage(), 422);
        }

        event(new AcademicEntityChanged('gradebook_entry', 'saved', $this->gradebookPayload($entry)));

        return $this->response->success(new GradebookEntryResource($entry), 'Gradebook entry saved successfully', 201);
    }

    public function show(string $id)
    {
        $entry = $this->service->findById($id);

        if (! $entry) {
            return $this->response->error('Gradebook entry not found', 404);
        }

        return $this->response->success(new GradebookEntryResource($entry), 'Gradebook entry retrieved successfully');
    }

    public function update(UpdateGradebookEntryRequest $request, string $id)
    {
        try {
            $entry = $this->service->update($id, $request->validated());
        } catch (GradeWeightingException $exception) {
            return $this->response->error($exception->getMessage(), 422);
        }

        if (! $entry) {
            return $this->response->error('Gradebook entry not found', 404);
        }

        event(new AcademicEntityChanged('gradebook_entry', 'updated', $this->gradebookPayload($entry)));

        return $this->response->success(new GradebookEntryResource($entry), 'Gradebook entry updated successfully');
    }

    public function destroy(string $id)
    {
        $entry = $this->service->findById($id);
        $deleted = $this->service->delete($id);

        if (! $deleted) {
            return $this->response->error('Gradebook entry not found', 404);
        }

        if ($entry) {
            event(new AcademicEntityChanged('gradebook_entry', 'deleted', $this->gradebookPayload($entry)));
        }

        return $this->response->success([], 'Gradebook entry deleted successfully');
    }

    protected function gradebookPayload(object $entry): array
    {
        return [
            'id' => $entry->id,
            'organization_id' => $entry->organization_id,
            'student_id' => $entry->student_id,
            'student_name' => $entry->student?->full_name ?? trim(($entry->student?->first_name ?? '').' '.($entry->student?->last_name ?? '')),
            'subject_id' => $entry->subject_id,
            'subject_name' => $entry->subject?->name,
            'class_id' => $entry->class_id,
            'class_name' => $entry->academicClass?->name,
            'academic_session' => $entry->academic_session,
            'term' => $entry->term,
            'component' => $entry->component?->value ?? $entry->component,
            'score' => (float) $entry->score,
            'max_score' => (float) $entry->max_score,
            'weight' => (float) $entry->weight,
        ];
    }
}
