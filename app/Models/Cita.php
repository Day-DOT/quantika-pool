<?php

namespace App\Models;

use App\Enums\EstadoCita;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cita extends Model
{
    use HasFactory;

    protected $fillable = [
        'horario_id',
        'alumno_id',
        'sucursal_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estado',
        'asistio',
        'notas',
        'registrado_por',
        'reposicion_de_id',
        'es_extra',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'estado' => EstadoCita::class,
            'asistio' => 'boolean',
            'es_extra' => 'boolean',
        ];
    }

    public function horario(): BelongsTo
    {
        return $this->belongsTo(Horario::class);
    }

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function citaOriginal(): BelongsTo
    {
        return $this->belongsTo(Cita::class, 'reposicion_de_id');
    }

    public function reposicion(): HasOne
    {
        return $this->hasOne(Cita::class, 'reposicion_de_id');
    }

    public function scopeDeSucursal($query, int $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }

    public function scopeDelDia($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    /**
     * Clases de una sola fecha que ocupan lugar en un grupo sin que el
     * alumno esté inscrito en él: clases extra y reposiciones de faltas.
     */
    public function scopeVisitantesDelDia($query, int $horarioId, $fecha)
    {
        return $query->where('horario_id', $horarioId)
            ->whereDate('fecha', $fecha)
            ->where('estado', '!=', EstadoCita::Cancelada->value)
            ->where(fn ($q) => $q->where('es_extra', true)->orWhereNotNull('reposicion_de_id'));
    }
}
