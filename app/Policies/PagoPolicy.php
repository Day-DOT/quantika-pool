<?php

namespace App\Policies;

use App\Models\Pago;
use App\Models\User;

class PagoPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isAlumno();
    }

    public function view(User $user, Pago $pago): bool
    {
        if ($user->isAdmin()) {
            return $user->sucursal_id === $pago->sucursal_id;
        }

        return $user->isAlumno() && $user->id === $pago->alumno?->tutor_user_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Pago $pago): bool
    {
        return $user->isAdmin() && $user->sucursal_id === $pago->sucursal_id;
    }

    public function delete(User $user, Pago $pago): bool
    {
        return $user->isAdmin() && $user->sucursal_id === $pago->sucursal_id;
    }
}
