<?php

namespace App\Http\Requests\Alumno;

use App\Models\Alumno;
use Illuminate\Foundation\Http\FormRequest;

class ReservarClaseRequest extends FormRequest
{
    /**
     * Barrera adicional a la Policy del controlador: solo se puede
     * solicitar una reposición para un alumno del tutor autenticado.
     */
    public function authorize(): bool
    {
        $alumno = Alumno::find($this->input('alumno_id'));

        return $alumno !== null && $this->user()?->can('view', $alumno);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'alumno_id' => ['required', 'integer', 'exists:alumnos,id'],
            'cita_id' => ['required', 'integer', 'exists:citas,id'],
            'horario_id' => ['required', 'integer', 'exists:horarios,id'],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'alumno_id.required' => 'Selecciona para qué alumno es la reposición.',
            'cita_id.required' => 'Selecciona la falta que quieres reponer.',
            'horario_id.required' => 'Selecciona el grupo donde quieres reponer la clase.',
            'horario_id.exists' => 'El grupo seleccionado ya no está disponible.',
            'fecha.required' => 'Selecciona la fecha de la reposición.',
            'fecha.after_or_equal' => 'La reposición no puede programarse en una fecha pasada.',
        ];
    }
}
