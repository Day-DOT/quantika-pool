<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlumnoNivelHistorial extends Model
{
    use HasFactory;

    protected $table = 'alumno_nivel_historial';

    protected $fillable = [
        'alumno_id',
        'nivel_id',
        'fecha_inicio',
        'fecha_fin',
        'promovido_por',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class);
    }

    public function promovidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'promovido_por');
    }
}
