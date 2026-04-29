<?php

namespace Illimi\Academics\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Codizium\Core\Helpers\CoreJsonResponse;
use Illimi\Academics\Events\AcademicEntityChanged;
use Illimi\Academics\Requests\StoreClassRequest;
use Illimi\Academics\Requests\UpdateClassRequest;
use Illimi\Academics\Resources\ClassResource;
use Illimi\Academics\Services\ClassService;
use Illuminate\Http\Request;

class ClassController extends BaseController
{
    public function __construct(
        protected ClassService $service,
        protected CoreJsonResponse $response
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/classes",
     *     summary="List classes",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Classes retrieved")
     * )
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = array_filter([
            'level' => $request->query('level'),
            ...$request->only(['id', 'name', 'section_id', 'classroom_id', 'class_teacher_id']),
        ], fn ($value) => $value !== null && $value !== '');


        $classes = $this->service->list($filters, $perPage);

        return $this->response->success(ClassResource::collection($classes), 'Classes retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/academics/classes",
     *     summary="Create class",
     *     tags={"Academics"},
     *     @OA\Response(response=201, description="Class created")
     * )
     */
    public function store(StoreClassRequest $request)
    {
        $class = $this->service->create($request->validated());
        $class = $this->service->findById($class->id) ?? $class;

        event(AcademicEntityChanged::fromModel('academic_class', 'created', $class));

        return $this->response->success(new ClassResource($class), 'Class created successfully', 201);
    }

    public function show(string $id)
    {
        $class = $this->service->findById($id);

        if (! $class) {
            return $this->response->error('Class not found', 404);
        }

        return $this->response->success(new ClassResource($class), 'Class retrieved successfully');
    }

    public function update(UpdateClassRequest $request, string $id)
    {
        $class = $this->service->update($id, $request->validated());

        if (! $class) {
            return $this->response->error('Class not found', 404);
        }

        event(AcademicEntityChanged::fromModel('academic_class', 'updated', $class));

        return $this->response->success(new ClassResource($class), 'Class updated successfully');
    }

    public function destroy(string $id)
    {
        $class = $this->service->findById($id);
        $deleted = $this->service->delete($id);

        if (! $deleted) {
            return $this->response->error('Class not found', 404);
        }

        if ($class) {
            event(AcademicEntityChanged::fromModel('academic_class', 'deleted', $class));
        }

        return $this->response->success([], 'Class deleted successfully');
    }
}
