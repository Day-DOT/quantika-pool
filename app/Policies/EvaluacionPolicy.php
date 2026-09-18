<?php

namespace App\Policies;

use App\Models\Evaluacion;
use App\Models\User;

class EvaluacionPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Evaluacion $evaluacion): bool
    {
        if ($user->isAdmin()) {
            return $user->sucursal_id === $evaluacion->alumno?->sucursal_id;
        }

        if ($user->isInstructor()) {
            return $user->instructor !== null
                && $evaluacion->alumno?->inscripciones()
                    ->where('activa', true)
                    ->whereHas('horario', fn ($q) => $q->where('instructor_id', $user->instructor->id))
                    ->exists();
        }

        if ($user->isAlumno()) {
            return $user->id === $evaluacion->alumno?->tutor_user_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isInstructor();
    }

    public function update(User $user, Evaluacion $evaluacion): bool
    {
        return $user->isInstructor() && $this->view($user, $evaluacion);
    }

    public function delete(User $user, Evaluacion $evaluacion): bool
    {
        return $user->isInstructor() && $this->view($user, $evaluacion);
    }
}
