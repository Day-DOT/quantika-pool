<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nivel extends Model
{
    use HasFactory;

    protected $table = 'niveles';

    protected $fillable = [
        'orden',
        'nombre',
        'categoria',
        'descripcion',
        'color_hex',
        'imagen',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'orden' => 'integer',
            'activo' => 'boolean',
        ];
    }

    public function criterios(): HasMany
    {
        return $this->hasMany(CriterioEvaluacion::class);
    }

    public function alumnos(): HasMany
    {
        return $this->hasMany(Alumno::class);
    }

    public function historial(): HasMany
    {
        return $this->hasMany(AlumnoNivelHistorial::class);
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class);
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden');
    }

    public function siguiente(): ?self
    {
        return static::where('orden', '>', $this->orden)->orderBy('orden')->first();
    }
}
