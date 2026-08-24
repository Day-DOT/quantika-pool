<?php

namespace App\Policies;

use App\Models\Instructor;
use App\Models\User;

class InstructorPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function view(User $user, Instructor $instructor): bool
    {
        return $user->sucursal_id === $instructor->sucursal_id
            || $user->id === $instructor->user_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Instructor $instructor): bool
    {
        return $user->isAdmin() && $user->sucursal_id === $instructor->sucursal_id;
    }

    public function delete(User $user, Instructor $instructor): bool
    {
        return $user->isAdmin() && $user->sucursal_id === $instructor->sucursal_id;
    }
}
