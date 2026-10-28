<?php

namespace App\Enums;

enum EstadoClaseMuestra: string
{
    case Programada = 'programada';
    case Asistio = 'asistio';
    case NoAsistio = 'no_asistio';
    case Cancelada = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::Programada => 'Programada',
            self::Asistio => 'Asistió',
            self::NoAsistio => 'No asistió',
            self::Cancelada => 'Cancelada',
        };
    }
}
