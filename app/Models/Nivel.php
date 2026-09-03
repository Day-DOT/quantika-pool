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
        'categoria_edad',
        'total_sub_niveles',
        'descripcion',
        'color_hex',
        'imagen',
        'activo',
    ];

    public const CATEGORIAS_EDAD = ['Bebés', 'Niños', 'Adultos'];

    protected function casts(): array
    {
        return [
            'orden' => 'integer',
            'total_sub_niveles' => 'integer',
            'activo' => 'boolean',
        ];
    }

    public function tieneSubNiveles(): bool
    {
        return $this->total_sub_niveles > 1;
    }

    /**
     * Convierte un sub-nivel (1, 2, 3...) a su letra (A, B, C...).
     */
    public function etiquetaSubNivel(int $subNivel): string
    {
        return chr(64 + max(1, min($subNivel, 26)));
    }

    public function nombreConSubNivel(int $subNivel): string
    {
        return $this->tieneSubNiveles()
            ? "{$this->nombre} {$this->etiquetaSubNivel($subNivel)}"
            : $this->nombre;
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
        // CASE WHEN (no FIELD(), que es exclusivo de MySQL) para que el orden
        // funcione igual en MySQL y en SQLite (usado en las pruebas).
        return $query
            ->orderByRaw("CASE categoria_edad WHEN 'Bebés' THEN 1 WHEN 'Niños' THEN 2 WHEN 'Adultos' THEN 3 ELSE 4 END")
            ->orderBy('orden');
    }

    public function scopeDeCategoriaEdad($query, string $categoriaEdad)
    {
        return $query->where('categoria_edad', $categoriaEdad);
    }

    public function siguiente(): ?self
    {
        return static::where('categoria_edad', $this->categoria_edad)
            ->where('orden', '>', $this->orden)
            ->orderBy('orden')
            ->first();
    }
}
