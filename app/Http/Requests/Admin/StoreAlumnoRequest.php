<?php

namespace App\Http\Requests\Admin;

use App\Enums\Rol;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAlumnoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Alumno::class);
    }

    public function rules(): array
    {
        $esSuperAdmin = $this->user()->isSuperAdmin();

        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:150'],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
            'nivel_id' => ['nullable', 'integer', 'exists:niveles,id'],
            'sucursal_id' => [
                $esSuperAdmin ? 'required' : 'nullable',
                'integer',
                'exists:sucursales,id',
            ],
            'tutor_nombre' => ['required', 'string', 'max:150'],
            'tutor_email' => ['required', 'email', 'max:150'],
            'tutor_telefono' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'sucursal_id.required' => 'Selecciona la sucursal del alumno.',
        ];
    }
}
