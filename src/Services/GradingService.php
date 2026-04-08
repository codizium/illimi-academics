<?php

namespace Illimi\Academics\Services;

use Illimi\Academics\Events\ExamAutoGraded;
use Illimi\Academics\Models\ExamAnswer;
use Illimi\Academics\Models\ExamAttempt;
use Illimi\Academics\Models\Question;
use Illuminate\Support\Facades\Event;

class GradingService
{
    public function autoGrade(ExamAttempt $attempt): ExamAttempt
    {
        $attempt->loadMissing('exam');

        $answers = ExamAnswer::query()
            ->where('exam_attempt_id', $attempt->id)
            ->with('question')
            ->get();

        $score = 0.0;
        $autoGradable = true;

        foreach ($answers as $answer) {
            $question = $answer->question;

            if (! $question) {
                continue;
            }

            if (!in_array($question->type?->value ?? $question->type, ['mcq', 'true_false'], true)) {
                $autoGradable = false;
                continue;
            }

            $isCorrect = $this->isCorrectAnswer($question, $answer->answer);
            $marksAwarded = $isCorrect ? (float) $question->marks : 0.0;

            if (! $isCorrect && $attempt->exam?->negative_marking) {
                $marksAwarded = -1 * (float) ($attempt->exam->negative_mark_value ?? 0);
            }

            $answer->update([
                'is_correct' => $isCorrect,
                'marks_awarded' => $marksAwarded,
            ]);

            $score += $marksAwarded;
        }

        $attempt->update([
            'score' => $score,
            'is_auto_graded' => $autoGradable,
            'status' => $autoGradable ? 'graded' : 'submitted',
        ]);

        if ($autoGradable) {
            Event::dispatch(new ExamAutoGraded($attempt));
        }

        return $attempt->fresh();
    }

    protected function isCorrectAnswer(Question $question, $answer): bool
    {
        $correct = $question->correct_answer;

        if (is_array($correct)) {
            return in_array($answer, $correct, true);
        }

        return (string) $answer === (string) $correct;
    }
}
