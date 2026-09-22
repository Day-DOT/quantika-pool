<?php

namespace App\Http\Requests\Admin;

use App\Enums\Rol;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Nivel;

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
            'sexo' => ['nullable', Rule::in(Nivel::SEXOS)],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
            'nivel_id' => ['nullable', 'integer', 'exists:niveles,id'],
            'plan_id' => ['nullable', 'integer', 'exists:planes,id'],
            'sucursal_id' => [
                $esSuperAdmin ? 'required' : 'nullable',
                'integer',
                'exists:sucursales,id',
            ],
            'tiene_tutor' => ['nullable', 'boolean'],
            'tutor_nombre' => [$this->boolean('tiene_tutor') ? 'required' : 'nullable', 'string', 'max:150'],
            'tutor_email' => ['nullable', 'email', 'max:150'],
            'tutor_telefono' => ['nullable', 'string', 'max:20'],
            'ine_tutor' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'tipo_sangre' => ['nullable', 'string', 'max:5'],
            'contacto_emergencia_nombre' => ['nullable', 'string', 'max:150'],
            'contacto_emergencia_telefono' => ['nullable', 'string', 'max:20'],
            'observaciones_medicas' => ['nullable', 'string', 'max:2000'],
            'certificado_medico' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'identificacion' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'contrato_firmado' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'sucursal_id.required' => 'Selecciona la sucursal del alumno.',
        ];
    }
}
