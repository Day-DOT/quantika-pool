<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AvisarFaltaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('alumno'));
    }

    public function rules(): array
    {
        return [
            'horario_id' => ['required', 'integer', 'exists:horarios,id'],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'notas' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'horario_id.required' => 'Selecciona la clase a la que no asistirá.',
            'fecha.required' => 'Selecciona la fecha de la clase.',
            'fecha.after_or_equal' => 'El aviso de falta solo aplica para clases de hoy en adelante.',
        ];
    }
}
