<?php

namespace App\Enums;

enum ConceptoPago: string
{
    case Mensualidad = 'mensualidad';
    case Inscripcion = 'inscripcion';
    case Otro = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::Mensualidad => 'Mensualidad',
            self::Inscripcion => 'Inscripción',
            self::Otro => 'Concepto adicional',
        };
    }
}
