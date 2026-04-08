<?php

namespace Illimi\Academics\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Codizium\Core\Helpers\CoreJsonResponse;
use Illimi\Academics\Events\AcademicEntityChanged;
use Illimi\Academics\Services\SectionService;
use Illuminate\Http\Request;

class SectionController extends BaseController
{
    public function __construct(
        protected SectionService $service,
        protected CoreJsonResponse $response
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/sections",
     *     summary="List sections",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Sections retrieved")
     * )
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);

        $sections = $this->service->list([], $perPage);

        return $this->response->success($sections, 'Sections retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/academics/sections",
     *     summary="Create section",
     *     tags={"Academics"},
     *     @OA\Response(response=201, description="Section created")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $section = $this->service->create($validated);
        $section = $this->service->findById($section->id) ?? $section;

        event(AcademicEntityChanged::fromModel('section', 'created', $section));

        return $this->response->success($section, 'Section created successfully', 201);
    }

    public function show(string $id)
    {
        $section = $this->service->findById($id);

        if (!$section) {
            return $this->response->error('Section not found', 404);
        }

        return $this->response->success($section, 'Section retrieved successfully');
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        $section = $this->service->update($id, $validated);

        if (!$section) {
            return $this->response->error('Section not found', 404);
        }

        event(AcademicEntityChanged::fromModel('section', 'updated', $section));

        return $this->response->success($section, 'Section updated successfully');
    }

    public function destroy(string $id)
    {
        $section = $this->service->findById($id);
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            return $this->response->error('Section not found', 404);
        }

        if ($section) {
            event(AcademicEntityChanged::fromModel('section', 'deleted', $section));
        }

        return $this->response->success([], 'Section deleted successfully');
    }
}
