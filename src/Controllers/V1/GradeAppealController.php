<?php

namespace Illimi\Academics\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Codizium\Core\Helpers\CoreJsonResponse;
use Illimi\Academics\Events\AcademicEntityChanged;
use Illimi\Academics\Requests\ResolveGradeAppealRequest;
use Illimi\Academics\Requests\StoreGradeAppealRequest;
use Illimi\Academics\Resources\GradeAppealResource;
use Illimi\Academics\Services\GradeAppealService;

class GradeAppealController extends BaseController
{
    public function __construct(
        protected GradeAppealService $service,
        protected CoreJsonResponse $response
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/appeals",
     *     summary="List grade appeals",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Appeals retrieved")
     * )
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = array_filter([
            'student_id' => $request->query('student_id'),
            'result_id' => $request->query('result_id'),
            'status' => $request->query('status'),
        ], fn ($value) => $value !== null && $value !== '');

        $appeals = $this->service->list($filters, $perPage);

        return $this->response->success(GradeAppealResource::collection($appeals), 'Grade appeals retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/academics/appeals",
     *     summary="Submit grade appeal",
     *     tags={"Academics"},
     *     @OA\Response(response=201, description="Appeal submitted")
     * )
     */
    public function store(StoreGradeAppealRequest $request)
    {
        $appeal = $this->service->submit($request->validated());

        event(new AcademicEntityChanged('grade_appeal', 'created', $this->appealPayload($appeal)));

        return $this->response->success(new GradeAppealResource($appeal), 'Appeal submitted successfully', 201);
    }

    public function show(string $id)
    {
        $appeal = $this->service->findById($id);

        if (! $appeal) {
            return $this->response->error('Appeal not found', 404);
        }

        return $this->response->success(new GradeAppealResource($appeal), 'Appeal retrieved successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/v1/academics/appeals/{id}/resolve",
     *     summary="Resolve grade appeal",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Appeal resolved")
     * )
     */
    public function resolve(ResolveGradeAppealRequest $request, string $id)
    {
        $resolvedBy = auth()->id() ?? 'system';

        $appeal = $this->service->resolve($id, $request->validated(), $resolvedBy);

        if (! $appeal) {
            return $this->response->error('Appeal not found', 404);
        }

        event(new AcademicEntityChanged('grade_appeal', 'updated', $this->appealPayload($appeal)));

        return $this->response->success(new GradeAppealResource($appeal), 'Appeal resolved successfully');
    }

    public function destroy(string $id)
    {
        $appeal = $this->service->findById($id);
        $deleted = $this->service->delete($id);

        if (! $deleted) {
            return $this->response->error('Appeal not found', 404);
        }

        if ($appeal) {
            event(new AcademicEntityChanged('grade_appeal', 'deleted', $this->appealPayload($appeal)));
        }

        return $this->response->success([], 'Appeal deleted successfully');
    }

    protected function appealPayload(object $appeal): array
    {
        $resultLabel = trim(implode(' - ', array_filter([
            $appeal->result?->student?->full_name ?? trim(($appeal->result?->student?->first_name ?? '').' '.($appeal->result?->student?->last_name ?? '')),
            $appeal->result?->academicClass?->name,
            $appeal->result?->academic_session,
            $appeal->result?->term,
        ])));

        return [
            'id' => $appeal->id,
            'organization_id' => $appeal->organization_id,
            'result_id' => $appeal->result_id,
            'result_label' => $resultLabel,
            'student_id' => $appeal->student_id,
            'student_name' => $appeal->student?->full_name ?? trim(($appeal->student?->first_name ?? '').' '.($appeal->student?->last_name ?? '')),
            'reason' => $appeal->reason,
            'status' => $appeal->status?->value ?? $appeal->status,
            'resolution' => $appeal->resolution,
            'submitted_at' => $appeal->submitted_at?->toIso8601String(),
            'resolved_at' => $appeal->resolved_at?->toIso8601String(),
        ];
    }
}
