<?php

namespace Illimi\Academics\Services;

use Illimi\Academics\Jobs\GenerateTranscriptPdfJob;
use Illimi\Academics\Models\Result;
use Illimi\Academics\Models\Transcript;
use Illuminate\Support\Facades\Config;

class TranscriptService
{
    public function getTranscript(string $studentId, string $academicSession, ?string $term = null): ?Transcript
    {
        return Transcript::query()
            ->where('student_id', $studentId)
            ->where('academic_session', $academicSession)
            ->when($term !== null, function ($query) use ($term) {
                $query->where('term', $term);
            })
            ->first();
    }

    public function generateTranscript(string $studentId, string $academicSession, ?string $term = null): Transcript
    {
        $existing = $this->getTranscript($studentId, $academicSession, $term);
        $ttl = (int) Config::get('illimi-academics.transcript_cache_ttl', 86400);

        if ($existing && $existing->file_path && $existing->generated_at && $existing->generated_at->diffInSeconds(now()) < $ttl) {
            return $existing;
        }

        $payload = $this->buildPayload($studentId, $academicSession, $term);

        $transcript = $existing ?? new Transcript();
        $transcript->fill([
            'student_id' => $studentId,
            'academic_session' => $academicSession,
            'term' => $term,
            'payload' => $payload,
            'generated_at' => now(),
        ]);
        $transcript->save();

        GenerateTranscriptPdfJob::dispatch($transcript->id);

        return $transcript->fresh();
    }

    protected function buildPayload(string $studentId, string $academicSession, ?string $term = null): array
    {
        $resultsQuery = Result::query()
            ->where('student_id', $studentId)
            ->where('academic_session', $academicSession);

        if ($term !== null) {
            $resultsQuery->where('term', $term);
        }

        $results = $resultsQuery->get();

        return [
            'student_id' => $studentId,
            'academic_session' => $academicSession,
            'term' => $term,
            'results' => $results->map(function (Result $result) {
                return [
                    'id' => $result->id,
                    'class_id' => $result->class_id,
                    'term' => $result->term,
                    'total_score' => $result->total_score,
                    'grade' => $result->grade,
                    'grade_point' => $result->grade_point,
                    'remark' => $result->remark,
                    'status' => $result->status?->value ?? $result->status,
                ];
            })->values()->all(),
        ];
    }
}
