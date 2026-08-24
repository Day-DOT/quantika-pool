<?php

namespace App\Http\Requests\Instructor;

use App\Enums\EstadoEvaluacionDetalle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActualizarEvaluacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // La autorización de dominio (¿es esta evaluación / este alumno del
        // instructor?) se resuelve en el controlador con las Policies.
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'observaciones' => ['nullable', 'string', 'max:2000'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.criterio_evaluacion_id' => ['required', 'integer', 'exists:criterios_evaluacion,id'],
            'detalles.*.estado' => [
                'required',
                Rule::in(array_map(fn (EstadoEvaluacionDetalle $estado) => $estado->value, EstadoEvaluacionDetalle::cases())),
            ],
            'detalles.*.observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
