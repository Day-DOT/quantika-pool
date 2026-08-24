<?php

namespace App\Http\Requests\Admin;

use App\Enums\DiaSemana;
use App\Support\SucursalContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHorarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Horario::class);
    }

    public function rules(): array
    {
        // El formulario de "nueva clase" solo pide sucursal_id cuando el
        // super admin está en vista global; con una sucursal activa en el
        // selector, el controlador la usa automáticamente.
        $requiereSucursal = $this->user()->isSuperAdmin() && SucursalContext::actualId() === null;

        return [
            'sucursal_id' => [
                $requiereSucursal ? 'required' : 'nullable',
                'integer',
                'exists:sucursales,id',
            ],
            'nombre_grupo' => ['required', 'string', 'max:100'],
            'nivel_id' => ['required', 'integer', 'exists:niveles,id'],
            'instructor_id' => ['required', 'integer', 'exists:instructores,id'],
            'carril_id' => ['required', 'integer', 'exists:carriles,id'],
            'dia_semana' => ['required', 'integer', Rule::in(array_map(fn ($c) => $c->value, DiaSemana::cases()))],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'capacidad_maxima' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }
}
