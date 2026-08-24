<?php

namespace App\Policies;

use App\Models\Horario;
use App\Models\User;

class HorarioPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Horario $horario): bool
    {
        return $user->sucursal_id === $horario->sucursal_id
            || $user->instructor?->id === $horario->instructor_id
            || $user->isAlumno();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Horario $horario): bool
    {
        return $user->isAdmin() && $user->sucursal_id === $horario->sucursal_id;
    }

    public function delete(User $user, Horario $horario): bool
    {
        return $user->isAdmin() && $user->sucursal_id === $horario->sucursal_id;
    }
}
