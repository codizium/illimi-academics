<?php

namespace Illimi\Academics\Services;

use Illimi\Academics\Models\ExamAnswer;
use Illimi\Academics\Models\ExamAttempt;

class ItemAnalysisService
{
    public function analyseExam(string $examId): array
    {
        $attempts = ExamAttempt::query()
            ->where('exam_id', $examId)
            ->whereNotNull('score')
            ->orderByDesc('score')
            ->get();

        $answers = ExamAnswer::query()
            ->whereIn('exam_attempt_id', $attempts->pluck('id'))
            ->get()
            ->groupBy('question_id');

        $split = max(1, (int) floor($attempts->count() / 3));
        $topIds = $attempts->take($split)->pluck('id')->all();
        $bottomIds = $attempts->takeLast($split)->pluck('id')->all();

        $analysis = [];

        foreach ($answers as $questionId => $questionAnswers) {
            $total = $questionAnswers->count();
            $correct = $questionAnswers->where('is_correct', true)->count();

            $difficultyIndex = $total > 0 ? $correct / $total : 0;

            $topCorrect = $questionAnswers->whereIn('exam_attempt_id', $topIds)->where('is_correct', true)->count();
            $bottomCorrect = $questionAnswers->whereIn('exam_attempt_id', $bottomIds)->where('is_correct', true)->count();

            $discriminationIndex = $split > 0
                ? ($topCorrect / $split) - ($bottomCorrect / $split)
                : 0;

            $analysis[] = [
                'question_id' => $questionId,
                'difficulty_index' => round($difficultyIndex, 4),
                'discrimination_index' => round($discriminationIndex, 4),
            ];
        }

        return $analysis;
    }
}
