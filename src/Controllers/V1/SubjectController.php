<?php

namespace Illimi\Academics\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Codizium\Core\Helpers\CoreJsonResponse;
use Illimi\Academics\Events\AcademicEntityChanged;
use Illimi\Academics\Requests\StoreSubjectRequest;
use Illimi\Academics\Requests\UpdateSubjectRequest;
use Illimi\Academics\Resources\SubjectResource;
use Illimi\Academics\Services\SubjectService;
use Illuminate\Http\Request;

class SubjectController extends BaseController
{
    public function __construct(
        protected SubjectService $service,
        protected CoreJsonResponse $response
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/subjects",
     *     summary="List subjects",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Subjects retrieved")
     * )
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $subjects = $this->service->list([], $perPage);

        return $this->response->success(SubjectResource::collection($subjects), 'Subjects retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/academics/subjects",
     *     summary="Create subject",
     *     tags={"Academics"},
     *     @OA\Response(response=201, description="Subject created")
     * )
     */
    public function store(StoreSubjectRequest $request)
    {
        $subject = $this->service->create($request->validated());

        event(new AcademicEntityChanged('subject', 'created', $this->subjectPayload($subject)));

        return $this->response->success(new SubjectResource($subject), 'Subject created successfully', 201);
    }

    public function show(string $id)
    {
        $subject = $this->service->findById($id);

        if (! $subject) {
            return $this->response->error('Subject not found', 404);
        }

        return $this->response->success(new SubjectResource($subject), 'Subject retrieved successfully');
    }

    public function update(UpdateSubjectRequest $request, string $id)
    {
        $subject = $this->service->update($id, $request->validated());

        if (! $subject) {
            return $this->response->error('Subject not found', 404);
        }

        event(new AcademicEntityChanged('subject', 'updated', $this->subjectPayload($subject)));

        return $this->response->success(new SubjectResource($subject), 'Subject updated successfully');
    }

    public function destroy(string $id)
    {
        $subject = $this->service->findById($id);
        $deleted = $this->service->delete($id);

        if (! $deleted) {
            return $this->response->error('Subject not found', 404);
        }

        if ($subject) {
            event(new AcademicEntityChanged('subject', 'deleted', $this->subjectPayload($subject)));
        }

        return $this->response->success([], 'Subject deleted successfully');
    }

    protected function subjectPayload(object $subject): array
    {
        return [
            'id' => $subject->id,
            'organization_id' => $subject->organization_id,
            'name' => $subject->name,
            'code' => $subject->code,
            'description' => $subject->description,
            'credit_units' => $subject->credit_units,
            'teacher_ids' => $subject->teachers?->pluck('id')->values()->all() ?? [],
            'teacher_names' => $subject->teachers?->map(fn ($teacher) => $teacher->full_name ?? trim(($teacher->first_name ?? '').' '.($teacher->last_name ?? '')))->values()->all() ?? [],
            'class_ids' => $subject->classes?->pluck('id')->values()->all() ?? [],
            'class_names' => $subject->classes?->pluck('name')->values()->all() ?? [],
            'current_syllabus_id' => $subject->currentSyllabus?->id,
            'current_syllabus_title' => $subject->currentSyllabus?->title,
        ];
    }
}
