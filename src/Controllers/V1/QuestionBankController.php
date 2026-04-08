<?php

namespace Illimi\Academics\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Codizium\Core\Helpers\CoreJsonResponse;
use Illimi\Academics\Events\AcademicEntityChanged;
use Illimi\Academics\Requests\StoreQuestionBankRequest;
use Illimi\Academics\Requests\UpdateQuestionBankRequest;
use Illimi\Academics\Resources\QuestionBankResource;
use Illimi\Academics\Services\QuestionBankService;
use Illuminate\Http\Request;

class QuestionBankController extends BaseController
{
    public function __construct(
        protected QuestionBankService $service,
        protected CoreJsonResponse $response
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/question-banks",
     *     summary="List question banks",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Question banks retrieved")
     * )
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $banks = $this->service->listQuestionBanks($perPage);

        return $this->response->success(QuestionBankResource::collection($banks), 'Question banks retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/academics/question-banks",
     *     summary="Create question bank",
     *     tags={"Academics"},
     *     @OA\Response(response=201, description="Question bank created")
     * )
     */
    public function store(StoreQuestionBankRequest $request)
    {
        $questionBank = $this->service->createQuestionBank($request->validated());
        $questionBank = $this->service->findQuestionBankById($questionBank->id) ?? $questionBank;

        event(new AcademicEntityChanged('question_bank', 'created', $this->questionBankPayload($questionBank)));

        return $this->response->success(new QuestionBankResource($questionBank), 'Question bank created successfully', 201);
    }

    public function show(string $id)
    {
        $questionBank = $this->service->findQuestionBankById($id);

        if (! $questionBank) {
            return $this->response->error('Question bank not found', 404);
        }

        return $this->response->success(new QuestionBankResource($questionBank), 'Question bank retrieved successfully');
    }

    public function update(UpdateQuestionBankRequest $request, string $id)
    {
        $questionBank = $this->service->updateQuestionBank($id, $request->validated());

        if (! $questionBank) {
            return $this->response->error('Question bank not found', 404);
        }

        event(new AcademicEntityChanged('question_bank', 'updated', $this->questionBankPayload($questionBank)));

        return $this->response->success(new QuestionBankResource($questionBank), 'Question bank updated successfully');
    }

    public function destroy(string $id)
    {
        $questionBank = $this->service->findQuestionBankById($id);
        $deleted = $this->service->deleteQuestionBank($id);

        if (! $deleted) {
            return $this->response->error('Question bank not found', 404);
        }

        if ($questionBank) {
            event(new AcademicEntityChanged('question_bank', 'deleted', $this->questionBankPayload($questionBank)));
        }

        return $this->response->success([], 'Question bank deleted successfully');
    }

    protected function questionBankPayload(object $questionBank): array
    {
        return [
            'id' => $questionBank->id,
            'organization_id' => $questionBank->organization_id,
            'name' => $questionBank->name,
            'description' => $questionBank->description,
            'subject_id' => $questionBank->subject_id,
            'subject_name' => $questionBank->subject?->name,
            'class_id' => $questionBank->class_id,
            'class_name' => $questionBank->academicClass?->name,
            'questions_count' => $questionBank->questions_count ?? $questionBank->questions()?->count() ?? 0,
        ];
    }
}
