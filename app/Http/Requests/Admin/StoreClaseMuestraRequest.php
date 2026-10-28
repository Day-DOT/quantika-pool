<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreClaseMuestraRequest extends FormRequest
{
    /**
     * Las rutas de administración ya limitan el acceso a admin y super admin.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_alumno' => ['required', 'string', 'max:120'],
            'edad' => ['required', 'integer', 'min:0', 'max:99'],
            'nombre_tutor' => ['required', 'string', 'max:120'],
            'telefono' => ['required', 'string', 'max:30', 'regex:/^(\D*\d){10}/'],
            'email' => ['nullable', 'email', 'max:150'],
            'horario_id' => ['required', 'integer', 'exists:horarios,id'],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'notas' => ['nullable', 'string', 'max:500'],
            'confirmar_repetida' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_alumno.required' => 'Escribe el nombre del alumno.',
            'edad.required' => 'Escribe la edad del alumno.',
            'nombre_tutor.required' => 'Escribe el nombre del tutor.',
            'telefono.required' => 'Escribe el teléfono del tutor.',
            'telefono.regex' => 'El teléfono debe tener al menos 10 dígitos.',
            'email.email' => 'El correo electrónico no es válido.',
            'horario_id.required' => 'Selecciona el grupo donde tomará la clase muestra.',
            'fecha.required' => 'Selecciona la fecha de la clase muestra.',
            'fecha.after_or_equal' => 'La clase muestra no puede agendarse en una fecha pasada.',
        ];
    }
}
