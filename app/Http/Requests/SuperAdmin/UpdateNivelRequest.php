<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNivelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $nivel = $this->route('nivel');

        return [
            'orden' => ['required', 'integer', 'min:1', 'max:255', Rule::unique('niveles', 'orden')->ignore($nivel)],
            'nombre' => ['required', 'string', 'max:255'],
            'categoria' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'color_hex' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'imagen' => ['nullable', 'image', 'max:2048'],
            'activo' => ['required', 'boolean'],
        ];
    }
}
