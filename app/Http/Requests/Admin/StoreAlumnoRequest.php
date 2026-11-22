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
            // Con fecha de corte se exige plan: sin plan la mensualidad no se
            // vuelve a agendar al pagarse y el ciclo se cortaría.
            'plan_id' => ['nullable', 'integer', 'exists:planes,id', 'required_with:fecha_corte_mensualidad'],
            // Fechas de pago de la inscripción: el corte de la mensualidad
            // usa el precio del plan y la anualidad lleva su propio monto.
            'fecha_corte_mensualidad' => ['nullable', 'date', 'required_with:monto_mensualidad'],
            'monto_mensualidad' => ['nullable', 'numeric', 'min:0', 'max:99999', 'required_with:fecha_corte_mensualidad'],
            'fecha_pago_anualidad' => ['nullable', 'date', 'required_with:monto_anualidad'],
            'monto_anualidad' => ['nullable', 'numeric', 'min:0', 'max:99999', 'required_with:fecha_pago_anualidad'],
            // Días y horarios: grupos en los que queda inscrito al guardar.
            'horario_ids' => ['nullable', 'array', 'max:7'],
            'horario_ids.*' => ['integer', 'distinct', 'exists:horarios,id'],
            'sucursal_id' => [
                $esSuperAdmin ? 'required' : 'nullable',
                'integer',
                'exists:sucursales,id',
            ],
            'tiene_tutor' => ['nullable', 'boolean'],
            'tutor_nombre' => [$this->boolean('tiene_tutor') ? 'required' : 'nullable', 'string', 'max:150'],
            'tutor_email' => ['nullable', 'email', 'max:150'],
            'tutor_telefono' => ['nullable', 'string', 'max:20'],
            'ine_tutor_frente' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'ine_tutor_reverso' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
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
            'plan_id.required_with' => 'Elige el plan de mensualidad para poder agendar su fecha de corte.',
            'monto_mensualidad.required_with' => 'Escribe el monto de la mensualidad.',
            'fecha_corte_mensualidad.required_with' => 'Elige la fecha de corte de la mensualidad.',
            'monto_anualidad.required_with' => 'Escribe el monto de la anualidad.',
            'fecha_pago_anualidad.required_with' => 'Elige la fecha de pago de la anualidad.',
            'horario_ids.*.exists' => 'Uno de los horarios elegidos ya no existe.',
        ];
    }
}
