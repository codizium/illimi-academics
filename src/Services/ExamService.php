<?php

namespace Illimi\Academics\Services;

use Illimi\Academics\Events\ExamStarted;
use Illimi\Academics\Events\ExamSubmitted;
use Illimi\Academics\Exceptions\ExamNotFoundException;
use Illimi\Academics\Jobs\AutoGradeExamJob;
use Illimi\Academics\Models\Exam;
use Illimi\Academics\Models\ExamAnswer;
use Illimi\Academics\Models\ExamAttempt;
use Illimi\Academics\Models\Question;
use Illimi\Academics\Support\ExamRandomiser;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;

class ExamService
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Exam::query()->with(['subject', 'academicClass']);

        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (!empty($filters['academic_session'])) {
            $query->where('academic_session', $filters['academic_session']);
        }

        if (!empty($filters['term'])) {
            $query->where('term', $filters['term']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): Exam
    {
        $data['randomise_questions'] = $data['randomise_questions'] ?? Config::get('illimi-academics.randomise_questions', true);
        $data['randomise_options'] = $data['randomise_options'] ?? Config::get('illimi-academics.randomise_options', true);
        $data['allow_review'] = $data['allow_review'] ?? Config::get('illimi-academics.allow_review', true);
        $data['status'] = $data['status'] ?? 'scheduled';

        return Exam::create($data);
    }

    public function update(string $id, array $data): ?Exam
    {
        $exam = Exam::find($id);

        if (! $exam) {
            return null;
        }

        $exam->update($data);

        return $this->findById($exam->id);
    }

    public function findById(string $id): ?Exam
    {
        return Exam::query()
            ->with(['subject', 'academicClass'])
            ->find($id);
    }

    public function delete(string $id): bool
    {
        $exam = Exam::find($id);

        if (! $exam) {
            return false;
        }

        return (bool) $exam->delete();
    }

    public function startExam(string $examId, string $studentId, array $context = []): ExamAttempt
    {
        $exam = Exam::find($examId);

        if (! $exam) {
            throw new ExamNotFoundException($examId);
        }

        if ($exam->starts_at && now()->lt($exam->starts_at)) {
            throw new \RuntimeException('Exam has not started yet.');
        }

        if ($exam->ends_at && now()->gt($exam->ends_at)) {
            throw new \RuntimeException('Exam has ended.');
        }

        $existing = ExamAttempt::query()
            ->where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->where('status', 'in_progress')
            ->first();

        if ($existing) {
            return $existing;
        }

        $attempt = ExamAttempt::create([
            'exam_id' => $examId,
            'student_id' => $studentId,
            'started_at' => now(),
            'status' => 'in_progress',
            'ip_address' => $context['ip_address'] ?? request()->ip(),
            'browser_info' => $context['browser_info'] ?? null,
        ]);

        Event::dispatch(new ExamStarted($attempt));

        return $attempt->fresh();
    }

    public function submitExam(string $examId, string $studentId, array $answers): ExamAttempt
    {
        $exam = Exam::find($examId);

        if (! $exam) {
            throw new ExamNotFoundException($examId);
        }

        if ($exam->ends_at && now()->gt($exam->ends_at)) {
            throw new \RuntimeException('Exam submission window has closed.');
        }

        $attempt = ExamAttempt::query()
            ->where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->orderByDesc('created_at')
            ->first();

        if (! $attempt) {
            throw new \RuntimeException('No active attempt found for this exam.');
        }

        foreach ($answers as $answer) {
            ExamAnswer::updateOrCreate([
                'exam_attempt_id' => $attempt->id,
                'question_id' => $answer['question_id'],
            ], [
                'answer' => $answer['answer'],
            ]);
        }

        $attempt->update([
            'submitted_at' => now(),
            'time_taken_seconds' => $attempt->started_at ? now()->diffInSeconds($attempt->started_at) : null,
            'status' => 'submitted',
        ]);

        Event::dispatch(new ExamSubmitted($attempt));
        AutoGradeExamJob::dispatch($attempt->id);

        return $attempt->fresh();
    }

    public function getResults(string $examId, int $perPage = 15): LengthAwarePaginator
    {
        return ExamAttempt::query()
            ->where('exam_id', $examId)
            ->latest()
            ->paginate($perPage);
    }

    public function listAttempts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ExamAttempt::query();

        if (!empty($filters['exam_id'])) {
            $query->where('exam_id', $filters['exam_id']);
        }

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function getItemAnalysis(string $examId): array
    {
        return app(ItemAnalysisService::class)->analyseExam($examId);
    }

    public function getExamQuestions(Exam $exam, ?string $studentId = null): array
    {
        $questions = Question::query()
            ->where('subject_id', $exam->subject_id)
            ->get()
            ->all();

        return ExamRandomiser::shuffleQuestions($questions, $exam->randomise_questions, $studentId);
    }
}
