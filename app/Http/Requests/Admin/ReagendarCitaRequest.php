<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReagendarCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('cita'));
    }

    public function rules(): array
    {
        return [
            'horario_id' => ['required', 'integer', 'exists:horarios,id'],
            'fecha' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'horario_id.required' => 'Selecciona el nuevo horario para esta clase.',
            'fecha.required' => 'Selecciona la nueva fecha de la clase.',
        ];
    }
}
