<?php

namespace Illimi\Academics\Scopes;

use Illuminate\Database\Eloquent\Builder;

class TeacherScope
{
    protected const BYPASS_ROLES = [
        'super-admin',
        'organization-admin',
        'admin',
        'principal',
        'hod',
    ];

    public static function shouldBypass(mixed $user = null): bool
    {
        $user ??= auth()->user();
        if (! $user) {
            return true;
        }
        return method_exists($user, 'hasAnyRole') && $user->hasAnyRole(self::BYPASS_ROLES);
    }

    public static function apply(Builder $query, mixed $user = null, string $teacherRelation = 'teachers', string $teacherUserColumn = 'user_id'): Builder
    {
        if (self::shouldBypass($user)) {
            return $query;
        }
        
        $user ??= auth()->user();

        return $query->whereHas($teacherRelation, function (Builder $teacherQuery) use ($teacherUserColumn, $user) {
            $teacherQuery->where($teacherUserColumn, $user->id);
        });
    }
}
