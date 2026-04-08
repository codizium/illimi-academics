<?php

namespace Illimi\Academics\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Codizium\Core\Helpers\CoreJsonResponse;
use Illimi\Academics\Events\AcademicEntityChanged;
use Illimi\Academics\Requests\StoreGradeScaleRequest;
use Illimi\Academics\Requests\UpdateGradeScaleRequest;
use Illimi\Academics\Resources\GradeScaleResource;
use Illimi\Academics\Services\GradeScaleService;
use Illuminate\Http\Request;

class GradeScaleController extends BaseController
{
    public function __construct(
        protected GradeScaleService $service,
        protected CoreJsonResponse $response
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/grade-scales",
     *     summary="List grade scales",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Grade scales retrieved")
     * )
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $scales = $this->service->list($perPage);

        return $this->response->success(GradeScaleResource::collection($scales), 'Grade scales retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/academics/grade-scales",
     *     summary="Create grade scale",
     *     tags={"Academics"},
     *     @OA\Response(response=201, description="Grade scale created")
     * )
     */
    public function store(StoreGradeScaleRequest $request)
    {
        $scale = $this->service->create($request->validated());

        event(AcademicEntityChanged::fromModel('grade_scale', 'created', $scale));

        return $this->response->success(new GradeScaleResource($scale), 'Grade scale created successfully', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/grade-scales/{id}",
     *     summary="Show grade scale",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Grade scale retrieved")
     * )
     */
    public function show(string $id)
    {
        $scale = $this->service->findById($id);

        if (! $scale) {
            return $this->response->error('Grade scale not found', 404);
        }

        return $this->response->success(new GradeScaleResource($scale), 'Grade scale retrieved successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/v1/academics/grade-scales/{id}",
     *     summary="Update grade scale",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Grade scale updated")
     * )
     */
    public function update(UpdateGradeScaleRequest $request, string $id)
    {
        $existing = $this->service->findById($id);
        $scale = $this->service->update($id, $request->validated());

        if (! $scale) {
            return $this->response->error('Grade scale not found', 404);
        }

        event(AcademicEntityChanged::fromModel('grade_scale', 'updated', $scale, [
            'is_default' => $existing?->is_default,
        ]));

        return $this->response->success(new GradeScaleResource($scale), 'Grade scale updated successfully');
    }

    public function destroy(string $id)
    {
        $scale = $this->service->findById($id);
        $deleted = $this->service->delete($id);

        if (! $deleted) {
            return $this->response->error('Grade scale not found', 404);
        }

        if ($scale) {
            event(AcademicEntityChanged::fromModel('grade_scale', 'deleted', $scale));
        }

        return $this->response->success([], 'Grade scale deleted successfully');
    }
}
