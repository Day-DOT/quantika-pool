<?php

namespace App\Policies;

use App\Models\Cita;
use App\Models\User;

class CitaPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Cita $cita): bool
    {
        return $this->puedeGestionar($user, $cita);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isAlumno();
    }

    public function update(User $user, Cita $cita): bool
    {
        return $this->puedeGestionar($user, $cita);
    }

    public function delete(User $user, Cita $cita): bool
    {
        return $this->puedeGestionar($user, $cita);
    }

    private function puedeGestionar(User $user, Cita $cita): bool
    {
        if ($user->isAdmin()) {
            return $user->sucursal_id === $cita->sucursal_id;
        }

        if ($user->isInstructor()) {
            return $user->instructor?->id === $cita->horario?->instructor_id;
        }

        if ($user->isAlumno()) {
            return $user->id === $cita->alumno?->tutor_user_id;
        }

        return false;
    }
}
