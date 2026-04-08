<?php

namespace Illimi\Academics\Policies;

use Illimi\Academics\Models\Result;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResultPolicy
{
    use HandlesAuthorization;

    public function viewAny($user): bool
    {
        return true;
    }

    public function view($user, Result $result): bool
    {
        return true;
    }

    public function publish($user): bool
    {
        return in_array($user->role ?? null, ['principal', 'hod', 'admin'], true);
    }
}
