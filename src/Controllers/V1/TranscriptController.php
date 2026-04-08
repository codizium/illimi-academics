<?php

namespace Illimi\Academics\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Codizium\Core\Helpers\CoreJsonResponse;
use Illimi\Academics\Requests\GenerateTranscriptRequest;
use Illimi\Academics\Resources\TranscriptResource;
use Illimi\Academics\Services\TranscriptService;
use Illuminate\Http\Request;

class TranscriptController extends BaseController
{
    public function __construct(
        protected TranscriptService $service,
        protected CoreJsonResponse $response
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/transcripts/{studentId}",
     *     summary="Get student transcript",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Transcript retrieved")
     * )
     */
    public function show(Request $request, string $studentId)
    {
        $academicSession = $request->query('academic_session');
        $term = $request->query('term');

        if (! $academicSession) {
            return $this->response->error('academic_session query parameter is required', 422);
        }

        $transcript = $this->service->getTranscript($studentId, $academicSession, $term);

        if (! $transcript) {
            return $this->response->error('Transcript not found', 404);
        }

        return $this->response->success(new TranscriptResource($transcript), 'Transcript retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/academics/transcripts/{studentId}/generate",
     *     summary="Generate transcript",
     *     tags={"Academics"},
     *     @OA\Response(response=201, description="Transcript generation started")
     * )
     */
    public function generate(GenerateTranscriptRequest $request, string $studentId)
    {
        $payload = $request->validated();

        $transcript = $this->service->generateTranscript($studentId, $payload['academic_session'], $payload['term'] ?? null);

        return $this->response->success(new TranscriptResource($transcript), 'Transcript generated successfully', 201);
    }
}
