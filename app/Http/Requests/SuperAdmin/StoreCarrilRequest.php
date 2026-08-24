<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCarrilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sucursal_id' => ['required', 'integer', 'exists:sucursales,id'],
            'nombre' => [
                'required', 'string', 'max:255',
                Rule::unique('carriles', 'nombre')->where('sucursal_id', $this->input('sucursal_id')),
            ],
            'capacidad_maxima' => ['required', 'integer', 'min:1', 'max:100'],
            'activo' => ['required', 'boolean'],
        ];
    }
}
