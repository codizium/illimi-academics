<?php

namespace Illimi\Academics\Services;

use Illimi\Academics\Models\LessonPlan;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class LessonPlanService
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = LessonPlan::query()
            ->with(['schemeOfWork.syllabus.subject', 'schemeOfWork.academicTerm', 'teacher', 'attachments']);

        if (! empty($filters['scheme_of_work_id'])) {
            $query->where('scheme_of_work_id', $filters['scheme_of_work_id']);
        }
        if (! empty($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest('date')->paginate($perPage);
    }

    public function create(array $data): LessonPlan
    {
        return DB::transaction(function () use ($data) {
            $documents = $this->extractDocuments($data);

            $plan = LessonPlan::create(Arr::except($data, ['documents']));

            $this->attachDocuments($plan, $documents);

            return $this->findById($plan->id) ?? $plan->fresh();
        });
    }

    public function update(string $id, array $data): ?LessonPlan
    {
        $plan = LessonPlan::find($id);

        if (! $plan) {
            return null;
        }

        return DB::transaction(function () use ($plan, $data) {
            $documents = $this->extractDocuments($data);
            $removeIds = collect($data['remove_attachment_ids'] ?? [])->filter()->values()->all();

            $plan->update(Arr::except($data, ['documents', 'remove_attachment_ids']));

            foreach ($removeIds as $attachmentId) {
                if ($plan->attachments()->whereKey($attachmentId)->exists()) {
                    $plan->deleteAttachment($attachmentId, true);
                }
            }

            $this->attachDocuments($plan, $documents);

            return $this->findById($plan->id) ?? $plan->fresh();
        });
    }

    public function findById(string $id): ?LessonPlan
    {
        return LessonPlan::query()
            ->with(['schemeOfWork.syllabus.subject', 'schemeOfWork.academicTerm', 'teacher', 'attachments'])
            ->find($id);
    }

    public function delete(string $id): bool
    {
        $plan = LessonPlan::find($id);

        if (! $plan) {
            return false;
        }

        foreach ($plan->attachments()->pluck('id') as $attachmentId) {
            $plan->deleteAttachment($attachmentId, true);
        }

        return (bool) $plan->delete();
    }

    protected function extractDocuments(array &$data): array
    {
        $documents = Arr::wrap($data['documents'] ?? []);
        unset($data['documents']);

        return array_values(array_filter($documents, fn ($d) => $d instanceof UploadedFile));
    }

    protected function attachDocuments(LessonPlan $plan, array $documents): void
    {
        foreach ($documents as $document) {
            $plan->attach(
                $document,
                $document->getClientOriginalName(),
                'public',
                sprintf('academics/%s/lesson-plans/%s', $plan->organization_id ?? 'shared', $plan->id)
            );
        }
    }
}
