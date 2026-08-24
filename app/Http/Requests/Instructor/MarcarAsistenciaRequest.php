<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class MarcarAsistenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // La autorización de dominio (¿es este grupo del instructor?) se
        // resuelve en el controlador con las Policies correspondientes.
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'asistio' => ['required', 'boolean'],
            'notas' => ['nullable', 'string', 'max:500'],
        ];
    }
}
