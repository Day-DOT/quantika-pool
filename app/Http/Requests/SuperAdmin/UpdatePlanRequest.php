<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'clases_por_semana' => ['required', 'integer', Rule::in([2, 3])],
            'precio' => ['nullable', 'numeric', 'min:0'],
            'activo' => ['required', 'boolean'],
        ];
    }
}
