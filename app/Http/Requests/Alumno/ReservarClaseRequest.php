<?php

namespace App\Http\Requests\Alumno;

use App\Models\Alumno;
use Illuminate\Foundation\Http\FormRequest;

class ReservarClaseRequest extends FormRequest
{
    /**
     * Barrera adicional a la Policy del controlador: solo se puede
     * reservar una clase para un alumno que sea hijo del tutor autenticado.
     */
    public function authorize(): bool
    {
        $alumno = Alumno::find($this->input('alumno_id'));

        return $alumno !== null && $alumno->tutor_user_id === $this->user()?->id;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'alumno_id' => ['required', 'integer', 'exists:alumnos,id'],
            'horario_ids' => ['required', 'array', 'min:1'],
            'horario_ids.*' => ['integer', 'distinct', 'exists:horarios,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'alumno_id.required' => 'Selecciona a qué alumno se reservará la clase.',
            'horario_ids.required' => 'Selecciona al menos un horario para reservar.',
            'horario_ids.*.exists' => 'Uno de los horarios seleccionados ya no está disponible.',
        ];
    }
}
