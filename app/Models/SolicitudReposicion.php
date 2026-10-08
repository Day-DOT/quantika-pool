<?php

namespace App\Models;

use App\Enums\EstadoInscripcion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Solicitud de un tutor, hecha desde el portal, para reponer una falta en
 * un grupo y fecha concretos. No ocupa lugar hasta que la administración
 * la aprueba; al aprobarla se crea la Cita de reposición.
 */
class SolicitudReposicion extends Model
{
    protected $table = 'solicitudes_reposicion';

    protected $fillable = [
        'cita_id',
        'alumno_id',
        'horario_id',
        'fecha',
        'estado',
        'resuelta_por',
        'resuelta_en',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'estado' => EstadoInscripcion::class,
            'resuelta_en' => 'datetime',
        ];
    }

    public function falta(): BelongsTo
    {
        return $this->belongsTo(Cita::class, 'cita_id');
    }

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }

    public function horario(): BelongsTo
    {
        return $this->belongsTo(Horario::class);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', EstadoInscripcion::Pendiente->value);
    }
}
