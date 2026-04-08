<?php

namespace Illimi\Academics\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Codizium\Core\Helpers\CoreJsonResponse;
use Illimi\Academics\Events\AcademicEntityChanged;
use Illimi\Academics\Requests\StoreSyllabusRequest;
use Illimi\Academics\Requests\UpdateSyllabusRequest;
use Illimi\Academics\Resources\SyllabusResource;
use Illimi\Academics\Services\SyllabusService;
use Illuminate\Http\Request;

class SyllabusController extends BaseController
{
    public function __construct(
        protected SyllabusService $service,
        protected CoreJsonResponse $response
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/syllabi",
     *     summary="List syllabi",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Syllabi retrieved")
     * )
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = array_filter([
            'subject_id' => $request->query('subject_id'),
            'is_published' => $request->query('is_published'),
        ], fn ($value) => $value !== null && $value !== '');

        $syllabi = $this->service->list($filters, $perPage);

        return $this->response->success(SyllabusResource::collection($syllabi), 'Syllabi retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/academics/syllabi",
     *     summary="Create syllabus",
     *     tags={"Academics"},
     *     @OA\Response(response=201, description="Syllabus created")
     * )
     */
    public function store(StoreSyllabusRequest $request)
    {
        $syllabus = $this->service->create($request->validated());
        $syllabus = $this->service->findById($syllabus->id) ?? $syllabus;

        event(AcademicEntityChanged::fromModel('syllabus', 'created', $syllabus));

        return $this->response->success(new SyllabusResource($syllabus), 'Syllabus created successfully', 201);
    }

    public function show(string $id)
    {
        $syllabus = $this->service->findById($id);

        if (!$syllabus) {
            return $this->response->error('Syllabus not found', 404);
        }

        return $this->response->success(new SyllabusResource($syllabus), 'Syllabus retrieved successfully');
    }

    public function update(UpdateSyllabusRequest $request, string $id)
    {
        $syllabus = $this->service->update($id, $request->validated());

        if (! $syllabus) {
            return $this->response->error('Syllabus not found', 404);
        }

        $syllabus = $this->service->findById($syllabus->id) ?? $syllabus;

        event(AcademicEntityChanged::fromModel('syllabus', 'updated', $syllabus));

        return $this->response->success(new SyllabusResource($syllabus), 'Syllabus updated successfully');
    }

    public function destroy(string $id)
    {
        $syllabus = $this->service->findById($id);
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            return $this->response->error('Syllabus not found', 404);
        }

        if ($syllabus) {
            event(AcademicEntityChanged::fromModel('syllabus', 'deleted', $syllabus));
        }

        return $this->response->success([], 'Syllabus deleted successfully');
    }
}
