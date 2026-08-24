<?php

namespace App\Policies;

use App\Models\Sucursal;
use App\Models\User;

class SucursalPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function view(User $user, Sucursal $sucursal): bool
    {
        return $user->sucursal_id === $sucursal->id
            || $user->instructor?->sucursal_id === $sucursal->id;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Sucursal $sucursal): bool
    {
        return false;
    }

    public function delete(User $user, Sucursal $sucursal): bool
    {
        return false;
    }
}
