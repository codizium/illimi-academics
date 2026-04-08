<?php

namespace Illimi\Academics\Requests\Concerns;

use Illimi\Academics\Models\QuestionBank;
use Illimi\Academics\Models\Subject;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

trait ValidatesAcademicRelationships
{
    protected function organizationId(): ?string
    {
        return optional(function_exists('organization') ? organization() : null)->id
            ?? auth()->user()?->organization_id;
    }

    protected function scopedExists(string $table, string $column = 'id'): Exists
    {
        $rule = Rule::exists($table, $column);

        if ($organizationId = $this->organizationId()) {
            $rule->where(fn ($query) => $query->where('organization_id', $organizationId));
        }

        return $rule;
    }

    protected function validateSubjectClassPair(
        Validator $validator,
        ?string $subjectId,
        ?string $classId,
        string $classField = 'class_id'
    ): void {
        if (! $subjectId || ! $classId) {
            return;
        }

        $subjectQuery = Subject::query()
            ->select(['id', 'organization_id'])
            ->with('classes:id')
            ->whereKey($subjectId);

        if ($organizationId = $this->organizationId()) {
            $subjectQuery->where('organization_id', $organizationId);
        }

        $subject = $subjectQuery->first();

        if (! $subject) {
            return;
        }

        if (! $subject->classes->contains('id', $classId)) {
            $validator->errors()->add($classField, 'The selected class is not assigned to the selected subject.');
        }
    }

    protected function validateQuestionBankSubjectPair(
        Validator $validator,
        ?string $questionBankId,
        ?string $subjectId,
        string $questionBankField = 'question_bank_id',
        string $subjectField = 'subject_id'
    ): void {
        if (! $questionBankId || ! $subjectId) {
            return;
        }

        $questionBankQuery = QuestionBank::query()
            ->select(['id', 'organization_id', 'subject_id'])
            ->whereKey($questionBankId);

        if ($organizationId = $this->organizationId()) {
            $questionBankQuery->where('organization_id', $organizationId);
        }

        $questionBank = $questionBankQuery->first();

        if (! $questionBank || ! $questionBank->subject_id) {
            return;
        }

        if ($questionBank->subject_id !== $subjectId) {
            $validator->errors()->add($questionBankField, 'The selected question bank is assigned to a different subject.');
            $validator->errors()->add($subjectField, 'The selected subject does not match the chosen question bank.');
        }
    }
}
