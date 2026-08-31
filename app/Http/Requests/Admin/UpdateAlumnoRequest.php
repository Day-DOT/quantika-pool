<?php

namespace App\Http\Requests\Admin;

use App\Enums\EstadoAlumno;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAlumnoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('alumno'));
    }

    public function rules(): array
    {
        $tutor = $this->route('alumno')->tutorUser;

        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:150'],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
            'nivel_id' => ['nullable', 'integer', 'exists:niveles,id'],
            'plan_id' => ['nullable', 'integer', 'exists:planes,id'],
            'estado' => ['required', Rule::in(array_map(fn ($c) => $c->value, EstadoAlumno::cases()))],
            'tutor_nombre' => ['required', 'string', 'max:150'],
            'tutor_email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($tutor?->id),
            ],
            'tutor_telefono' => ['nullable', 'string', 'max:20'],
            'certificado_medico' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'identificacion' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }
}
