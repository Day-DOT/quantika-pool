<?php

namespace App\Http\Requests\Admin;

use App\Enums\MetodoPago;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClaseExtraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('alumno'));
    }

    public function rules(): array
    {
        return [
            'horario_id' => ['required', 'integer', 'exists:horarios,id'],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'monto' => ['nullable', 'numeric', 'min:0', 'max:99999'],
            'metodo_pago' => ['nullable', Rule::enum(MetodoPago::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'horario_id.required' => 'Selecciona el grupo donde tomará la clase extra.',
            'fecha.required' => 'Selecciona la fecha de la clase extra.',
            'fecha.after_or_equal' => 'La clase extra no puede registrarse en una fecha pasada.',
        ];
    }
}
