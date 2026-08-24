<?php

namespace App\Models;

use App\Enums\DiaSemana;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = [
        'sucursal_id',
        'nivel_id',
        'instructor_id',
        'carril_id',
        'nombre_grupo',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'capacidad_maxima',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'dia_semana' => DiaSemana::class,
            'capacidad_maxima' => 'integer',
            'activo' => 'boolean',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }

    public function carril(): BelongsTo
    {
        return $this->belongsTo(Carril::class);
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    public function scopeDeSucursal($query, int $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }

    public function scopeDelInstructor($query, int $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }
}
