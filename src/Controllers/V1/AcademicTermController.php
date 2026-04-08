<?php

namespace Illimi\Academics\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Codizium\Core\Helpers\CoreJsonResponse;
use Illimi\Academics\Events\AcademicEntityChanged;
use Illimi\Academics\Models\AcademicTerm;
use Illimi\Academics\Requests\StoreAcademicTermRequest;
use Illimi\Academics\Requests\UpdateAcademicTermRequest;
use Illimi\Academics\Resources\AcademicTermResource;
use Illimi\Academics\Services\AcademicTermService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AcademicTermController extends BaseController
{
    public function __construct(
        protected AcademicTermService $service,
        protected CoreJsonResponse $response
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/terms",
     *     summary="List academic terms",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Terms retrieved")
     * )
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = array_filter([
            'academic_year_id' => $request->query('academic_year_id'),
            'status' => $request->query('status'),
        ], fn ($value) => $value !== null && $value !== '');

        $terms = $this->service->list($filters, $perPage);

        return $this->response->success(AcademicTermResource::collection($terms), 'Academic terms retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/academics/terms",
     *     summary="Create academic term",
     *     tags={"Academics"},
     *     @OA\Response(response=201, description="Term created")
     * )
     */
    public function store(StoreAcademicTermRequest $request)
    {
        $term = $this->service->create($request->validated());
        $term = $this->service->findById($term->id) ?? $term;

        event(AcademicEntityChanged::fromModel('academic_term', 'created', $term));

        return $this->response->success(new AcademicTermResource($term), 'Academic term created successfully', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/terms/{id}",
     *     summary="Show academic term",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Term retrieved")
     * )
     */
    public function show(string $id)
    {
        $term = $this->service->findById($id);

        if (! $term) {
            return $this->response->error('Academic term not found', 404);
        }

        return $this->response->success(new AcademicTermResource($term), 'Academic term retrieved successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/v1/academics/terms/{id}",
     *     summary="Update academic term",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Term updated")
     * )
     */
    public function update(UpdateAcademicTermRequest $request, string $id)
    {
        $existing = $this->service->findById($id);
        $term = $this->service->update($id, $request->validated());

        if (! $term) {
            return $this->response->error('Academic term not found', 404);
        }

        $term = $this->service->findById($term->id) ?? $term;

        event(AcademicEntityChanged::fromModel('academic_term', 'updated', $term, [
            'status' => $existing?->status,
            'academic_year_id' => $existing?->academic_year_id,
            'start_date' => $existing?->start_date?->toIso8601String(),
            'end_date' => $existing?->end_date?->toIso8601String(),
        ]));

        return $this->response->success(new AcademicTermResource($term), 'Academic term updated successfully');
    }

    public function destroy(string $id)
    {
        $term = $this->service->findById($id);
        $deleted = $this->service->delete($id);

        if (! $deleted) {
            return $this->response->error('Academic term not found', 404);
        }

        if ($term) {
            event(AcademicEntityChanged::fromModel('academic_term', 'deleted', $term));
        }

        return $this->response->success([], 'Academic term deleted successfully');
    }

    public function changeAcademicTerm(Request $request)
    {
        $request->validate(['id' => 'required|uuid']);
        $academicTerm = AcademicTerm::findOrFail($request->id);
        app()->instance(AcademicTerm::class, $academicTerm);
        Session::put('academic_term', $academicTerm);

        return $this->response->success(new AcademicTermResource($academicTerm), 'Term change successfully');
    }
}
