<?php

namespace Illimi\Academics\Policies;

use Illimi\Academics\Models\Exam;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamPolicy
{
    use HandlesAuthorization;

    protected const STAFF_ROLES = ['admin', 'super-admin', 'organization-admin', 'principal', 'hod', 'teacher', 'staff'];

    public function viewAny($user): bool
    {
        return $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(self::STAFF_ROLES);
    }

    public function view($user, Exam $exam): bool
    {
        if (!$user) {
            return false;
        }
        // Staff can always view
        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(self::STAFF_ROLES)) {
            return true;
        }
        // Students can only view exams in their enrolled class
        if ($user->hasRole('student')) {
            return $exam->class_id && $user->student?->class_id === $exam->class_id;
        }
        return false;
    }

    public function create($user): bool
    {
        return $user && method_exists($user, 'hasAnyRole')
            && $user->hasAnyRole(['teacher', 'principal', 'admin', 'hod', 'super-admin']);
    }

    public function start($user, Exam $exam): bool
    {
        if (!$user) {
            return false;
        }
        // Only students can sit an exam
        if ($user->hasRole('student')) {
            return $exam->class_id && $user->student?->class_id === $exam->class_id;
        }
        return false;
    }
}
