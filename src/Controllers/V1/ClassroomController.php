<?php

namespace Illimi\Academics\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Codizium\Core\Helpers\CoreJsonResponse;
use Illimi\Academics\Events\AcademicEntityChanged;
use Illimi\Academics\Resources\ClassroomResource;
use Illimi\Academics\Services\ClassroomService;
use Illuminate\Http\Request;

class ClassroomController extends BaseController
{
    public function __construct(
        protected ClassroomService $service,
        protected CoreJsonResponse $response
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/classrooms",
     *     summary="List classrooms",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Classrooms retrieved")
     * )
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);

        $classrooms = $this->service->list([], $perPage);

        return $this->response->success(ClassroomResource::collection($classrooms), 'Classrooms retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/academics/classrooms",
     *     summary="Create classroom",
     *     tags={"Academics"},
     *     @OA\Response(response=201, description="Classroom created")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:illimi_classrooms,code',
            'capacity' => 'nullable|integer|min:0',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $classroom = $this->service->create($validated);
        $classroom = $this->service->findById($classroom->id) ?? $classroom;

        event(AcademicEntityChanged::fromModel('classroom', 'created', $classroom));

        return $this->response->success(new ClassroomResource($classroom), 'Classroom created successfully', 201);
    }

    public function show(string $id)
    {
        $classroom = $this->service->findById($id);

        if (!$classroom) {
            return $this->response->error('Classroom not found', 404);
        }

        return $this->response->success(new ClassroomResource($classroom), 'Classroom retrieved successfully');
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:50|unique:illimi_classrooms,code,' . $id,
            'capacity' => 'nullable|integer|min:0',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $classroom = $this->service->update($id, $validated);

        if (!$classroom) {
            return $this->response->error('Classroom not found', 404);
        }

        event(AcademicEntityChanged::fromModel('classroom', 'updated', $classroom));

        return $this->response->success(new ClassroomResource($classroom), 'Classroom updated successfully');
    }

    public function destroy(string $id)
    {
        $classroom = $this->service->findById($id);
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            return $this->response->error('Classroom not found', 404);
        }

        if ($classroom) {
            event(AcademicEntityChanged::fromModel('classroom', 'deleted', $classroom));
        }

        return $this->response->success([], 'Classroom deleted successfully');
    }
}
