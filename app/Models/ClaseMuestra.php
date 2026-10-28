<?php

namespace App\Models;

use App\Enums\EstadoClaseMuestra;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Clase gratuita de prueba para alguien que todavía no es alumno. Ocupa un
 * lugar en el grupo solo esa fecha. Los registros no se borran: son el
 * historial para reconocer a quien ya tomó su clase muestra.
 */
class ClaseMuestra extends Model
{
    protected $table = 'clases_muestra';

    protected $fillable = [
        'sucursal_id',
        'horario_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'nombre_alumno',
        'edad',
        'nombre_tutor',
        'telefono',
        'email',
        'estado',
        'notas',
        'registrado_por',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'edad' => 'integer',
            'estado' => EstadoClaseMuestra::class,
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (ClaseMuestra $clase) {
            $clase->email = $clase->email ? Str::lower(trim($clase->email)) : null;
            $clase->telefono_normalizado = self::normalizarTelefono($clase->telefono);
            $clase->nombre_normalizado = self::normalizarNombre($clase->nombre_alumno);
        });
    }

    public function horario(): BelongsTo
    {
        return $this->belongsTo(Horario::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    /**
     * Clases muestra que ocupan lugar en un grupo en una fecha.
     */
    public function scopeDelDia(Builder $query, int $horarioId, $fecha): Builder
    {
        return $query->where('horario_id', $horarioId)
            ->whereDate('fecha', $fecha)
            ->where('estado', '!=', EstadoClaseMuestra::Cancelada->value);
    }

    /**
     * Clases muestra anteriores (no canceladas, en cualquier sucursal) de la
     * misma persona: coincide el teléfono, el correo o el nombre del alumno.
     */
    public function scopeDeLaMismaPersona(Builder $query, string $telefono, ?string $email, string $nombreAlumno): Builder
    {
        $telefono = self::normalizarTelefono($telefono);
        $email = $email ? Str::lower(trim($email)) : null;

        return $query->where('estado', '!=', EstadoClaseMuestra::Cancelada->value)
            ->where(function (Builder $coincide) use ($telefono, $email, $nombreAlumno) {
                $coincide->where('nombre_normalizado', self::normalizarNombre($nombreAlumno));

                if ($telefono !== '') {
                    $coincide->orWhere('telefono_normalizado', $telefono);
                }

                if ($email) {
                    $coincide->orWhere('email', $email);
                }
            });
    }

    /**
     * Últimos 10 dígitos: "+52 (33) 1234-5678" y "3312345678" son el mismo.
     */
    public static function normalizarTelefono(?string $telefono): string
    {
        return substr(preg_replace('/\D+/', '', (string) $telefono), -10);
    }

    public static function normalizarNombre(?string $nombre): string
    {
        return trim(preg_replace('/\s+/', ' ', Str::lower(Str::ascii((string) $nombre))));
    }

    public function descripcionCorta(): string
    {
        return "{$this->nombre_alumno} ({$this->edad} ".($this->edad === 1 ? 'año' : 'años').')';
    }
}
