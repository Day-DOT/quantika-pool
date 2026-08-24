<?php

namespace App\Models;

use App\Enums\EstadoEvaluacionDetalle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluacionDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluacion_id',
        'criterio_evaluacion_id',
        'estado',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'estado' => EstadoEvaluacionDetalle::class,
        ];
    }

    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(Evaluacion::class);
    }

    public function criterio(): BelongsTo
    {
        return $this->belongsTo(CriterioEvaluacion::class, 'criterio_evaluacion_id');
    }
}
