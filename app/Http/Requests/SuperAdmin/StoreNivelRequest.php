<?php

namespace App\Http\Requests\SuperAdmin;

use App\Models\Nivel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNivelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'orden' => [
                'required',
                'integer',
                'min:1',
                'max:255',
                Rule::unique('niveles', 'orden')->where('categoria_edad', $this->input('categoria_edad')),
            ],
            'nombre' => ['required', 'string', 'max:255'],
            'categoria' => ['required', 'string', 'max:255'],
            'categoria_edad' => ['required', Rule::in(Nivel::CATEGORIAS_EDAD)],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'color_hex' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'imagen' => ['nullable', 'image', 'max:2048'],
            'activo' => ['required', 'boolean'],
        ];
    }
}
