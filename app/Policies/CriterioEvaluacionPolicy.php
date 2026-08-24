<?php

namespace App\Policies;

use App\Models\CriterioEvaluacion;
use App\Models\User;

class CriterioEvaluacionPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function view(User $user, CriterioEvaluacion $criterio): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, CriterioEvaluacion $criterio): bool
    {
        return false;
    }

    public function delete(User $user, CriterioEvaluacion $criterio): bool
    {
        return false;
    }
}
