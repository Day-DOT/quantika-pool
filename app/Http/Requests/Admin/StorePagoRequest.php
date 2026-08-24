<?php

namespace App\Http\Requests\Admin;

use App\Enums\ConceptoPago;
use App\Enums\EstadoPago;
use App\Enums\MetodoPago;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Pago::class);
    }

    public function rules(): array
    {
        return [
            'alumno_id' => ['required', 'integer', 'exists:alumnos,id'],
            'concepto' => ['required', Rule::in(array_map(fn ($c) => $c->value, ConceptoPago::cases()))],
            'periodo' => ['nullable', 'string', 'max:20'],
            'monto' => ['required', 'numeric', 'min:0'],
            'fecha_vencimiento' => ['nullable', 'date'],
            'fecha_pago' => ['nullable', 'date'],
            'metodo_pago' => ['nullable', Rule::in(array_map(fn ($c) => $c->value, MetodoPago::cases()))],
            'estado' => ['required', Rule::in(array_map(fn ($c) => $c->value, EstadoPago::cases()))],
            'comprobante' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
