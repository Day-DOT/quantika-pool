<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'clases_por_semana' => ['required', 'integer', 'min:1', 'max:7'],
            'precio' => ['nullable', 'numeric', 'min:0'],
            'activo' => ['required', 'boolean'],
        ];
    }
}
