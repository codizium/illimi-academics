<?php

namespace Illimi\Academics\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Codizium\Core\Helpers\CoreJsonResponse;
use Illimi\Academics\Events\AcademicEntityChanged;
use Illimi\Academics\Requests\StoreQuestionRequest;
use Illimi\Academics\Requests\UpdateQuestionRequest;
use Illimi\Academics\Resources\QuestionResource;
use Illimi\Academics\Services\QuestionBankService;

class QuestionController extends BaseController
{
    public function __construct(
        protected QuestionBankService $service,
        protected CoreJsonResponse $response
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academics/questions",
     *     summary="List questions",
     *     tags={"Academics"},
     *     @OA\Response(response=200, description="Questions retrieved")
     * )
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = array_filter([
            'question_bank_id' => $request->query('question_bank_id'),
            'subject_id' => $request->query('subject_id'),
            'difficulty' => $request->query('difficulty'),
            'type' => $request->query('type'),
        ], fn ($value) => $value !== null && $value !== '');

        $questions = $this->service->listQuestions($filters, $perPage);

        return $this->response->success(QuestionResource::collection($questions), 'Questions retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/academics/questions",
     *     summary="Create question",
     *     tags={"Academics"},
     *     @OA\Response(response=201, description="Question created")
     * )
     */
    public function store(StoreQuestionRequest $request)
    {
        $question = $this->service->createQuestion($request->validated());
        $question = $this->service->findQuestionById($question->id) ?? $question;

        event(new AcademicEntityChanged('question', 'created', $this->questionPayload($question)));

        return $this->response->success(new QuestionResource($question), 'Question created successfully', 201);
    }

    public function show(string $id)
    {
        $question = $this->service->findQuestionById($id);

        if (! $question) {
            return $this->response->error('Question not found', 404);
        }

        return $this->response->success(new QuestionResource($question), 'Question retrieved successfully');
    }

    public function update(UpdateQuestionRequest $request, string $id)
    {
        $question = $this->service->updateQuestion($id, $request->validated());

        if (! $question) {
            return $this->response->error('Question not found', 404);
        }

        event(new AcademicEntityChanged('question', 'updated', $this->questionPayload($question)));

        return $this->response->success(new QuestionResource($question), 'Question updated successfully');
    }

    public function destroy(string $id)
    {
        $question = $this->service->findQuestionById($id);
        $deleted = $this->service->deleteQuestion($id);

        if (! $deleted) {
            return $this->response->error('Question not found', 404);
        }

        if ($question) {
            event(new AcademicEntityChanged('question', 'deleted', $this->questionPayload($question)));
        }

        return $this->response->success([], 'Question deleted successfully');
    }

    protected function questionPayload(object $question): array
    {
        return [
            'id' => $question->id,
            'organization_id' => $question->organization_id,
            'question_bank_id' => $question->question_bank_id,
            'question_bank_name' => $question->questionBank?->name,
            'subject_id' => $question->subject_id,
            'subject_name' => $question->subject?->name,
            'type' => $question->type?->value ?? $question->type,
            'content' => $question->content,
            'options' => $question->options,
            'correct_answer' => $question->correct_answer,
            'explanation' => $question->explanation,
            'difficulty' => $question->difficulty,
            'marks' => $question->marks !== null ? (float) $question->marks : null,
        ];
    }
}
