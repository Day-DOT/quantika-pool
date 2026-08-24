<?php

namespace App\Http\Requests\Admin;

use App\Support\SucursalContext;
use Illuminate\Foundation\Http\FormRequest;

class StoreInstructorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Instructor::class);
    }

    public function rules(): array
    {
        // Solo se le pide sucursal_id al super admin cuando está en vista
        // global (sin sucursal elegida en el selector); si ya tiene una
        // sucursal activa, el controlador la usa automáticamente y el
        // formulario ni siquiera muestra el campo.
        $requiereSucursal = $this->user()->isSuperAdmin() && SucursalContext::actualId() === null;

        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'especialidad' => ['nullable', 'string', 'max:150'],
            'sucursal_id' => [
                $requiereSucursal ? 'required' : 'nullable',
                'integer',
                'exists:sucursales,id',
            ],
        ];
    }
}
