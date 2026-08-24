<?php

namespace App\Policies;

use App\Models\Alumno;
use App\Models\User;

class AlumnoPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor() || $user->isAlumno();
    }

    public function view(User $user, Alumno $alumno): bool
    {
        if ($user->isAdmin()) {
            return $user->sucursal_id === $alumno->sucursal_id;
        }

        if ($user->isInstructor()) {
            return $alumno->inscripciones()
                ->whereHas('horario', fn ($q) => $q->where('instructor_id', $user->instructor?->id))
                ->exists();
        }

        if ($user->isAlumno()) {
            return $user->id === $alumno->tutor_user_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isAlumno();
    }

    public function update(User $user, Alumno $alumno): bool
    {
        if ($user->isAdmin()) {
            return $user->sucursal_id === $alumno->sucursal_id;
        }

        return $user->isAlumno() && $user->id === $alumno->tutor_user_id;
    }

    public function delete(User $user, Alumno $alumno): bool
    {
        return $user->isAdmin() && $user->sucursal_id === $alumno->sucursal_id;
    }
}
