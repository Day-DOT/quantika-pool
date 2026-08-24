<?php

namespace App\Enums;

enum EstadoAlumno: string
{
    case Activo = 'activo';
    case Inactivo = 'inactivo';
    case BajaTemporal = 'baja_temporal';
    case BajaDefinitiva = 'baja_definitiva';

    public function label(): string
    {
        return match ($this) {
            self::Activo => 'Activo',
            self::Inactivo => 'Inactivo',
            self::BajaTemporal => 'Baja temporal',
            self::BajaDefinitiva => 'Baja definitiva',
        };
    }
}
