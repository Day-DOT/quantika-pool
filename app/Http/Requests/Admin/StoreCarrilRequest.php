<?php

namespace App\Http\Requests\Admin;

use App\Support\SucursalContext;
use Illuminate\Foundation\Http\FormRequest;

class StoreCarrilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Carril::class);
    }

    public function rules(): array
    {
        // El formulario solo muestra el campo de sucursal cuando el super
        // admin está en vista global; si ya tiene una sucursal activa en el
        // selector, el controlador la usa automáticamente.
        $requiereSucursal = $this->user()->isSuperAdmin() && SucursalContext::actualId() === null;

        return [
            'sucursal_id' => [
                $requiereSucursal ? 'required' : 'nullable',
                'integer',
                'exists:sucursales,id',
            ],
            'nombre' => ['required', 'string', 'max:50'],
            'capacidad_maxima' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }
}
