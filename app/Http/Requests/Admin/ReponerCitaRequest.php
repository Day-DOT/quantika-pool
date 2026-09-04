<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReponerCitaRequest extends FormRequest
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
            'horario_id.required' => 'Selecciona el horario donde se repondrá la clase.',
            'fecha.required' => 'Selecciona la fecha de la reposición.',
        ];
    }
}
