<?php

namespace Illimi\Academics\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Codizium\Core\Helpers\CoreJsonResponse;
use Illimi\Academics\Resources\ExamAttemptResource;
use Illimi\Academics\Services\ExamService;
use Illuminate\Http\Request;

class ExamAttemptController extends BaseController
{
    public function __construct(
        protected ExamService $service,
        protected CoreJsonResponse $response
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/exam-attempts",
     *     summary="List exam attempts",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Exam attempts retrieved")
     * )
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = array_filter([
            'exam_id' => $request->query('exam_id'),
            'student_id' => $request->query('student_id'),
            'status' => $request->query('status'),
        ], fn ($value) => $value !== null && $value !== '');

        $attempts = $this->service->listAttempts($filters, $perPage);

        return $this->response->success(ExamAttemptResource::collection($attempts), 'Exam attempts retrieved successfully');
    }
}
