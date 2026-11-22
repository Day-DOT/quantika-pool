<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Día inhábil o periodo de vacaciones (festivos, mantenimiento...) en que no
 * se pueden programar reposiciones. Sin sucursal aplica a todas.
 */
class FechaBloqueada extends Model
{
    protected $table = 'fechas_bloqueadas';

    protected $fillable = ['sucursal_id', 'fecha_inicio', 'fecha_fin', 'motivo', 'registrado_por'];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    /**
     * Bloqueos que aplican a una sucursal (los suyos y los generales).
     */
    public function scopeDeLaSucursal(Builder $query, ?int $sucursalId): Builder
    {
        return $query->where(fn ($q) => $q->whereNull('sucursal_id')->when($sucursalId, fn ($s) => $s->orWhere('sucursal_id', $sucursalId)));
    }

    /**
     * Bloqueo que cubre esa fecha en esa sucursal, o null si está libre.
     */
    public static function en(Carbon $fecha, ?int $sucursalId): ?self
    {
        return static::query()
            ->deLaSucursal($sucursalId)
            ->whereDate('fecha_inicio', '<=', $fecha)
            ->whereDate('fecha_fin', '>=', $fecha)
            ->orderBy('fecha_inicio')
            ->first();
    }

    public function descripcionDeFechas(): string
    {
        return $this->fecha_inicio->isSameDay($this->fecha_fin)
            ? $this->fecha_inicio->format('d/m/Y')
            : $this->fecha_inicio->format('d/m/Y').' al '.$this->fecha_fin->format('d/m/Y');
    }

    /**
     * Mensaje para quien intenta reponer en una fecha bloqueada.
     */
    public function mensaje(): string
    {
        return "No hay clases ni reposiciones el {$this->descripcionDeFechas()}: {$this->motivo}. Elige otra fecha.";
    }
}
