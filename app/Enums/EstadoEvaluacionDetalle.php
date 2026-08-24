<?php

namespace App\Enums;

enum EstadoEvaluacionDetalle: string
{
    case NoIniciado = 'no_iniciado';
    case EnProceso = 'en_proceso';
    case Logrado = 'logrado';

    public function label(): string
    {
        return match ($this) {
            self::NoIniciado => 'No iniciado',
            self::EnProceso => 'En proceso',
            self::Logrado => 'Logrado',
        };
    }
}
