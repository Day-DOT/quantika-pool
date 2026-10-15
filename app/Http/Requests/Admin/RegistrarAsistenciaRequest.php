<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarAsistenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('alumno'));
    }

    public function rules(): array
    {
        return [
            'horario_id' => ['required', 'integer', 'exists:horarios,id'],
            'fecha' => ['required', 'date', 'before_or_equal:today'],
            'asistio' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'horario_id.required' => 'Selecciona el grupo de la clase.',
            'fecha.required' => 'Selecciona la fecha de la clase.',
            'fecha.before_or_equal' => 'Solo se puede registrar la asistencia de clases de hoy o de días anteriores.',
        ];
    }
}
