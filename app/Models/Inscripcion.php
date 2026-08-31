<?php

namespace App\Models;

use App\Enums\EstadoInscripcion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripciones';

    protected $fillable = [
        'horario_id',
        'alumno_id',
        'fecha_inicio',
        'fecha_fin',
        'activa',
        'estado',
        'aprobado_por',
        'aprobado_en',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'activa' => 'boolean',
            'estado' => EstadoInscripcion::class,
            'aprobado_en' => 'datetime',
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

    public function aprobadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', EstadoInscripcion::Pendiente->value);
    }
}
