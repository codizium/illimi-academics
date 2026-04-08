<?php

namespace Illimi\Academics\Policies;

use Illimi\Academics\Models\Exam;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamPolicy
{
    use HandlesAuthorization;

    public function viewAny($user): bool
    {
        return true;
    }

    public function view($user, Exam $exam): bool
    {
        return true;
    }

    public function create($user): bool
    {
        return in_array($user->role ?? null, ['teacher', 'principal', 'admin', 'hod'], true);
    }

    public function start($user, Exam $exam): bool
    {
        return true;
    }
}
