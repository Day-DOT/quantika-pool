<?php

namespace App\Support;

use App\Enums\Rol;
use Illuminate\Support\Facades\Session;

/**
 * Resuelve la sucursal "actual" para alcance de datos.
 *
 * - Super admin: puede navegar entre sucursales (o ver todas si no ha elegido ninguna).
 * - Admin/Recepción e Instructor: siempre su sucursal asignada.
 * - Alumno/Tutor: no aplica (sus alumnos pueden estar en distintas sucursales).
 */
class SucursalContext
{
    private const SESSION_KEY = 'sucursal_actual_id';

    public static function actualId(): ?int
    {
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        return match ($user->role) {
            Rol::SuperAdmin => Session::get(self::SESSION_KEY),
            Rol::Admin => $user->sucursal_id,
            Rol::Instructor => $user->instructor?->sucursal_id,
            Rol::Alumno => null,
        };
    }

    public static function establecer(?int $sucursalId): void
    {
        Session::put(self::SESSION_KEY, $sucursalId);
    }

    public static function esVistaGlobal(): bool
    {
        return auth()->user()?->role === Rol::SuperAdmin && self::actualId() === null;
    }
}
