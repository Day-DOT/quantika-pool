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
        'subtitulo',
        'categoria',
        'categoria_edad',
        'total_sub_niveles',
        'descripcion',
        'color_hex',
        'imagen',
        'activo',
    ];

    public const CATEGORIAS_EDAD = ['Niños', 'Niñas', 'Adultos mujeres', 'Adultos hombres'];
    public const SEXOS = ['Mujer', 'Hombre'];

    public static function categoriaCompatibleConSexo(?string $categoria, ?string $sexo): bool
    {
        return ! in_array($categoria, ['Adultos mujeres', 'Adultos hombres'], true)
            || ($sexo === 'Mujer' && $categoria === 'Adultos mujeres')
            || ($sexo === 'Hombre' && $categoria === 'Adultos hombres');
    }

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
        // funcione igual en MySQL y en SQLite (usado en las pruebas). El
        // orden ya no es único, así que se usa "id" como desempate estable
        // entre niveles con el mismo número (p.ej. distintas etapas).
        return $query
            ->orderByRaw("CASE categoria_edad WHEN 'Niños' THEN 1 WHEN 'Niñas' THEN 2 WHEN 'Adultos mujeres' THEN 3 WHEN 'Adultos hombres' THEN 4 ELSE 5 END")
            ->orderBy('orden')
            ->orderBy('id');
    }

    public function scopeDeCategoriaEdad($query, string $categoriaEdad)
    {
        return $query->where('categoria_edad', $categoriaEdad);
    }

    public function siguiente(): ?self
    {
        // Primero busca otro nivel con el MISMO orden (p.ej. otra etapa del
        // mismo escalón, capturada como un registro aparte) antes de saltar
        // al siguiente número de orden.
        $mismoOrden = static::where('categoria_edad', $this->categoria_edad)
            ->where('orden', $this->orden)
            ->where('id', '>', $this->id)
            ->orderBy('id')
            ->first();

        if ($mismoOrden) {
            return $mismoOrden;
        }

        return static::where('categoria_edad', $this->categoria_edad)
            ->where('orden', '>', $this->orden)
            ->orderBy('orden')
            ->orderBy('id')
            ->first();
    }
}
