<?php

namespace App\Policies;

use App\Models\Carril;
use App\Models\User;

class CarrilPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function view(User $user, Carril $carril): bool
    {
        return $this->mismaSucursal($user, $carril->sucursal_id);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Carril $carril): bool
    {
        return $user->isAdmin() && $this->mismaSucursal($user, $carril->sucursal_id);
    }

    public function delete(User $user, Carril $carril): bool
    {
        return $user->isAdmin() && $this->mismaSucursal($user, $carril->sucursal_id);
    }

    private function mismaSucursal(User $user, int $sucursalId): bool
    {
        return $user->sucursal_id === $sucursalId
            || $user->instructor?->sucursal_id === $sucursalId;
    }
}
