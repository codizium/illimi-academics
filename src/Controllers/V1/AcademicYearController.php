<?php

namespace Illimi\Academics\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Codizium\Core\Helpers\CoreJsonResponse;
use Illimi\Academics\Events\AcademicEntityChanged;
use Illimi\Academics\Models\AcademicYear;
use Illimi\Academics\Requests\StoreAcademicYearRequest;
use Illimi\Academics\Requests\UpdateAcademicYearRequest;
use Illimi\Academics\Resources\AcademicYearResource;
use Illimi\Academics\Services\AcademicYearService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AcademicYearController extends BaseController
{
    public function __construct(
        protected AcademicYearService $service,
        protected CoreJsonResponse $response
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/academic-years",
     *     summary="List academic years",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Academic years retrieved")
     * )
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = array_filter([
            'status' => $request->query('status'),
            'name' => $request->query('name'),
        ], fn($value) => $value !== null && $value !== '');

        $years = $this->service->list($filters, $perPage);

        return $this->response->success(AcademicYearResource::collection($years), 'Academic years retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/academics/academic-years",
     *     summary="Create academic year",
     *     tags={"Academics"},
     *     @OA\Response(response=201, description="Academic year created")
     * )
     */
    public function store(StoreAcademicYearRequest $request)
    {
        $year = $this->service->create($request->validated());
        $year = $this->service->findById($year->id) ?? $year;

        event(AcademicEntityChanged::fromModel('academic_year', 'created', $year));

        return $this->response->success(new AcademicYearResource($year), 'Academic year created successfully', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/academic-years/{id}",
     *     summary="Show academic year",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Academic year retrieved")
     * )
     */
    public function show(string $id)
    {
        $year = $this->service->findById($id);

        if (!$year) {
            return $this->response->error('Academic year not found', 404);
        }

        return $this->response->success(new AcademicYearResource($year), 'Academic year retrieved successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/v1/academics/academic-years/{id}",
     *     summary="Update academic year",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Academic year updated")
     * )
     */
    public function update(UpdateAcademicYearRequest $request, string $id)
    {
        $existing = $this->service->findById($id);
        $year = $this->service->update($id, $request->validated());

        if (!$year) {
            return $this->response->error('Academic year not found', 404);
        }

        $year = $this->service->findById($year->id) ?? $year;

        event(AcademicEntityChanged::fromModel('academic_year', 'updated', $year, [
            'status' => $existing?->status,
            'start_date' => $existing?->start_date?->toIso8601String(),
            'end_date' => $existing?->end_date?->toIso8601String(),
        ]));

        return $this->response->success(new AcademicYearResource($year), 'Academic year updated successfully');
    }

    public function destroy(string $id)
    {
        $year = $this->service->findById($id);
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            return $this->response->error('Academic year not found', 404);
        }

        if ($year) {
            event(AcademicEntityChanged::fromModel('academic_year', 'deleted', $year));
        }

        return $this->response->success([], 'Academic year deleted successfully');
    }

    public function changeAcademicYear(Request $request)
    {
        $request->validate(['id' => 'required|uuid']);
        $academicYear = AcademicYear::findOrFail($request->id);

        // if ($academicYear->terms->count() <= 0) {
        //     return $this->response->error("Action failed, Academic year has no 'terms' ");
        // }

        app()->instance(AcademicYear::class, $academicYear);
        Session::put('academic_year', $academicYear);

        return $this->response->success(new AcademicYearResource($academicYear), 'Term change successfully');
    }
}
