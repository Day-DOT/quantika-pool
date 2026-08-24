<?php

namespace App\Models;

use App\Enums\ConceptoPago;
use App\Enums\EstadoPago;
use App\Enums\MetodoPago;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'alumno_id',
        'sucursal_id',
        'concepto',
        'periodo',
        'monto',
        'fecha_vencimiento',
        'fecha_pago',
        'metodo_pago',
        'estado',
        'comprobante_path',
        'observaciones',
        'registrado_por',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'fecha_vencimiento' => 'date',
            'fecha_pago' => 'date',
            'concepto' => ConceptoPago::class,
            'metodo_pago' => MetodoPago::class,
            'estado' => EstadoPago::class,
        ];
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

    public function scopeDeSucursal($query, int $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }

    public function scopeVencidos($query)
    {
        return $query->where('estado', EstadoPago::Pendiente->value)
            ->whereNotNull('fecha_vencimiento')
            ->whereDate('fecha_vencimiento', '<', now());
    }

    /**
     * Pagos pendientes cuya fecha de vencimiento cae dentro de los próximos $dias
     * (sin incluir los que ya vencieron, esos son scopeVencidos()).
     */
    public function scopeProximosAVencer($query, int $dias = 5)
    {
        return $query->where('estado', EstadoPago::Pendiente->value)
            ->whereNotNull('fecha_vencimiento')
            ->whereDate('fecha_vencimiento', '>=', now()->toDateString())
            ->whereDate('fecha_vencimiento', '<=', now()->addDays($dias)->toDateString());
    }
}
