<?php

namespace Illimi\Academics\Policies;

use Illimi\Academics\Models\Result;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResultPolicy
{
    use HandlesAuthorization;

    protected const STAFF_ROLES = ['admin', 'super-admin', 'organization-admin', 'principal', 'hod', 'teacher', 'staff'];

    public function viewAny($user): bool
    {
        return $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(self::STAFF_ROLES);
    }

    public function view($user, Result $result): bool
    {
        if (!$user) {
            return false;
        }

        // Staff always have access
        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(self::STAFF_ROLES)) {
            return true;
        }

        // A student can only view their own result
        if ($user->hasRole('student')) {
            return $result->student_id && $result->student?->user_id === $user->id;
        }

        // A parent can view results for their linked children
        if ($user->hasRole('parent')) {
            return $result->student?->parents()?->where('user_id', $user->id)->exists();
        }

        return false;
    }

    public function publish($user): bool
    {
        return $user && method_exists($user, 'hasAnyRole')
            && $user->hasAnyRole(['principal', 'hod', 'admin', 'super-admin']);
    }
}
