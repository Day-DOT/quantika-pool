<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instructor extends Model
{
    use HasFactory;

    protected $table = 'instructores';

    protected $fillable = [
        'user_id',
        'sucursal_id',
        'especialidad',
        'estado',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class);
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class);
    }

    /**
     * Alumnos inscritos en sus grupos activos a los que todavía no ha
     * evaluado en su nivel actual.
     */
    public function alumnosPendientesDeEvaluar(): Builder
    {
        return Alumno::query()
            ->whereNotNull('nivel_id')
            ->whereHas('inscripciones', fn ($inscripcion) => $inscripcion
                ->where('activa', true)
                ->whereHas('horario', fn ($horario) => $horario->where('instructor_id', $this->id)->where('activo', true)))
            ->whereDoesntHave('evaluaciones', fn ($evaluacion) => $evaluacion
                ->where('instructor_id', $this->id)
                ->whereColumn('nivel_id', 'alumnos.nivel_id'));
    }

    public function scopeDeSucursal($query, int $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }
}
