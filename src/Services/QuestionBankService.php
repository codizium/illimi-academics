<?php

namespace Illimi\Academics\Services;

use Illimi\Academics\Models\Question;
use Illimi\Academics\Models\QuestionBank;
use Illuminate\Pagination\LengthAwarePaginator;

class QuestionBankService
{
    public function listQuestionBanks(int $perPage = 15): LengthAwarePaginator
    {
        return QuestionBank::query()
            ->with(['subject', 'academicClass'])
            ->withCount('questions')
            ->latest()
            ->paginate($perPage);
    }

    public function createQuestionBank(array $data): QuestionBank
    {
        return QuestionBank::create($data);
    }

    public function updateQuestionBank(string $id, array $data): ?QuestionBank
    {
        $questionBank = QuestionBank::find($id);

        if (! $questionBank) {
            return null;
        }

        $questionBank->update($data);

        return $this->findQuestionBankById($questionBank->id);
    }

    public function findQuestionBankById(string $id): ?QuestionBank
    {
        return QuestionBank::query()
            ->with(['subject', 'academicClass'])
            ->withCount('questions')
            ->find($id);
    }

    public function deleteQuestionBank(string $id): bool
    {
        $questionBank = QuestionBank::find($id);

        if (! $questionBank) {
            return false;
        }

        return (bool) $questionBank->delete();
    }

    public function createQuestion(array $data): Question
    {
        return Question::create($data);
    }

    public function updateQuestion(string $id, array $data): ?Question
    {
        $question = Question::find($id);

        if (! $question) {
            return null;
        }

        $question->update($data);

        return $this->findQuestionById($question->id);
    }

    public function findQuestionById(string $id): ?Question
    {
        return Question::query()
            ->with(['questionBank', 'subject'])
            ->find($id);
    }

    public function deleteQuestion(string $id): bool
    {
        $question = Question::find($id);

        if (! $question) {
            return false;
        }

        return (bool) $question->delete();
    }

    public function listQuestions(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Question::query()->with(['questionBank', 'subject']);

        if (!empty($filters['question_bank_id'])) {
            $query->where('question_bank_id', $filters['question_bank_id']);
        }

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (!empty($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->latest()->paginate($perPage);
    }
}
