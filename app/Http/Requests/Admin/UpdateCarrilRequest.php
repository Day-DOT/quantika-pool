<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCarrilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('carril'));
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:50'],
            'capacidad_maxima' => ['required', 'integer', 'min:1', 'max:50'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
