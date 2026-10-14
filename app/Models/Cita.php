<?php

namespace App\Models;

use App\Enums\EstadoCita;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'falta_avisada',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'estado' => EstadoCita::class,
            'asistio' => 'boolean',
            'es_extra' => 'boolean',
            'falta_avisada' => 'boolean',
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

    /**
     * Solicitudes del portal en espera para reponer esta falta.
     */
    public function solicitudesPendientes(): HasMany
    {
        return $this->hasMany(SolicitudReposicion::class, 'cita_id')
            ->where('estado', \App\Enums\EstadoInscripcion::Pendiente->value);
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
     * alumno esté inscrito en él: clases extra, reposiciones de faltas y
     * clases reagendadas desde otro grupo.
     */
    public function scopeVisitantesDelDia($query, int $horarioId, $fecha)
    {
        return $query->where('citas.horario_id', $horarioId)
            ->whereDate('citas.fecha', $fecha)
            ->whereNotIn('citas.estado', [EstadoCita::Cancelada->value, EstadoCita::Reagendada->value])
            ->whereNotExists(fn ($inscrito) => $inscrito
                ->selectRaw('1')
                ->from('inscripciones')
                ->whereColumn('inscripciones.alumno_id', 'citas.alumno_id')
                ->whereColumn('inscripciones.horario_id', 'citas.horario_id')
                ->where('inscripciones.activa', true));
    }
}
