<?php

namespace Illimi\Academics\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Codizium\Core\Helpers\CoreJsonResponse;
use Illimi\Academics\Exceptions\ResultAlreadyPublishedException;
use Illimi\Academics\Exceptions\ResultPublicationBlockedException;
use Illimi\Academics\Models\Result;
use Illimi\Academics\Requests\PublishResultsRequest;
use Illimi\Academics\Resources\ResultCollection;
use Illimi\Academics\Resources\ResultResource;
use Illimi\Academics\Services\ResultService;
use Illuminate\Http\Request;

class ResultController extends BaseController
{
    public function __construct(
        protected ResultService $service,
        protected CoreJsonResponse $response
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/results",
     *     summary="List results",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Results retrieved")
     * )
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = array_filter([
            'student_id' => $request->query('student_id'),
            'class_id' => $request->query('class_id'),
            'status' => $request->query('status'),
            'academic_session' => $request->query('academic_session'),
            'term' => $request->query('term'),
        ], fn($value) => $value !== null && $value !== '');

        $results = $this->service->list($filters, $perPage);

        return $this->response->success(new ResultCollection($results), 'Results retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/results/{id}",
     *     summary="Show result",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Result retrieved")
     * )
     */
    public function show(string $id)
    {
        $result = $this->service->findById($id);

        if (!$result) {
            return $this->response->error('Result not found', 404);
        }

        return $this->response->success(new ResultResource($result), 'Result retrieved successfully');
    }

    public function publicationPreview(Request $request)
    {
        $request->validate([
            'class_id' => ['required', 'uuid'],
            'academic_year_id' => ['required', 'uuid'],
            'academic_term_id' => ['required', 'uuid'],
        ]);

        $preview = $this->service->publicationPreview(
            $request->query('class_id'),
            $request->query('academic_year_id'),
            $request->query('academic_term_id'),
            auth()->user()?->organization_id
        );

        return $this->response->success($preview, 'Publication preview retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/academics/results/publish",
     *     summary="Publish results",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Results published")
     * )
     */
    public function publish(PublishResultsRequest $request)
    {
        // $this->authorize('publish', Result::class);

        $publishedBy = auth()->id() ?? 'system';
        $payload = $request->validated();
        $resultIds = $payload['result_ids']
            ?? $this->service->resultIdsForScope(
                $payload['class_id'],
                $payload['academic_session'],
                $payload['term']
            );

        try {
            $count = $this->service->publish($resultIds, $publishedBy);
        } catch (ResultAlreadyPublishedException $exception) {
            return $this->response->error($exception->getMessage(), 409);
        } catch (ResultPublicationBlockedException $exception) {
            return $this->response->error($exception->getMessage(), 422);
        }

        return $this->response->success(['published' => $count], 'Results published successfully');
    }

    public function unpublish(Request $request)
    {
        // $this->authorize('publish', Result::class);

        $payload = $request->validate([
            'result_ids' => ['required', 'array', 'min:1'],
            'result_ids.*' => ['required', 'uuid', 'exists:illimi_results,id'],
        ]);

        $count = $this->service->unpublish($payload['result_ids']);

        return $this->response->success(['unpublished' => $count], 'Results unpublished successfully');
    }
}
