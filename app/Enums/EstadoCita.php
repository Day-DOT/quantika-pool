<?php

namespace App\Enums;

enum EstadoCita: string
{
    case Programada = 'programada';
    case Confirmada = 'confirmada';
    case Cancelada = 'cancelada';
    case Reagendada = 'reagendada';
    case Completada = 'completada';

    public function label(): string
    {
        return match ($this) {
            self::Programada => 'Programada',
            self::Confirmada => 'Confirmada',
            self::Cancelada => 'Cancelada',
            self::Reagendada => 'Reagendada',
            self::Completada => 'Completada',
        };
    }
}
