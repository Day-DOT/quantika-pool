<?php

namespace App\Http\Requests\Admin;

use App\Enums\DiaSemana;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReagendarHorarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('horario'));
    }

    public function rules(): array
    {
        return [
            'dia_semana' => ['required', 'integer', Rule::in(array_map(fn ($c) => $c->value, DiaSemana::cases()))],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'carril_id' => ['required', 'integer', 'exists:carriles,id'],
        ];
    }
}
