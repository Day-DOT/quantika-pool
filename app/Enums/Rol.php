<?php

namespace App\Enums;

enum Rol: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Instructor = 'instructor';
    case Alumno = 'alumno';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Administrador',
            self::Admin => 'Administrador',
            self::Instructor => 'Instructor',
            self::Alumno => 'Alumno / Tutor',
        };
    }
}
