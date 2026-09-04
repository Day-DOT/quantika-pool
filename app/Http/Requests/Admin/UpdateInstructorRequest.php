<?php

namespace App\Http\Requests\Admin;

use App\Support\SucursalContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInstructorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('instructor'));
    }

    public function rules(): array
    {
        $instructor = $this->route('instructor');

        // Solo el super admin, y solo en vista global, puede reasignar la
        // sucursal de un instructor ya creado.
        $puedeReasignarSucursal = $this->user()->isSuperAdmin() && SucursalContext::actualId() === null;

        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($instructor->user_id),
            ],
            'telefono' => ['nullable', 'string', 'max:20'],
            'especialidad' => ['nullable', 'string', 'max:150'],
            'sucursal_id' => [
                $puedeReasignarSucursal ? 'sometimes' : 'prohibited',
                'integer',
                'exists:sucursales,id',
            ],
        ];
    }
}
