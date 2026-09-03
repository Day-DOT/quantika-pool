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
            'tiene_tutor' => ['nullable', 'boolean'],
            'tutor_nombre' => [$this->boolean('tiene_tutor') ? 'required' : 'nullable', 'string', 'max:150'],
            // Sin restricción de único: si el correo ya pertenece a otro
            // tutor, el controlador reutiliza esa cuenta (así se enlazan
            // hermanos al mismo tutor, incluso editando después del alta).
            'tutor_email' => ['nullable', 'email', 'max:150'],
            'tutor_telefono' => ['nullable', 'string', 'max:20'],
            'certificado_medico' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'identificacion' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'contrato_firmado' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}
