<?php

namespace App\Enums;

enum EstadoPago: string
{
    case Pendiente = 'pendiente';
    case Pagado = 'pagado';
    case EnRevision = 'en_revision';
    case Vencido = 'vencido';

    public function label(): string
    {
        return match ($this) {
            self::Pendiente => 'Pendiente',
            self::Pagado => 'Pagado',
            self::EnRevision => 'En revisión',
            self::Vencido => 'Vencido',
        };
    }
}
