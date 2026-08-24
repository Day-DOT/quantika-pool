<?php

namespace App\Support;

use App\Enums\Rol;
use App\Models\User;

class RoleRedirect
{
    public static function homeRouteFor(User $user): string
    {
        return match ($user->role) {
            Rol::SuperAdmin => 'super-admin.dashboard',
            Rol::Admin => 'admin.dashboard',
            Rol::Instructor => 'instructor.dashboard',
            Rol::Alumno => 'portal.dashboard',
        };
    }
}
