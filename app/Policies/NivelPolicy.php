<?php

namespace App\Policies;

use App\Models\Nivel;
use App\Models\User;

class NivelPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Nivel $nivel): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Nivel $nivel): bool
    {
        return false;
    }

    public function delete(User $user, Nivel $nivel): bool
    {
        return false;
    }
}
