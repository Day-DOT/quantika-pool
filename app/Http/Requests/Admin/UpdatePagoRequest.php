<?php

namespace App\Http\Requests\Admin;

class UpdatePagoRequest extends StorePagoRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('pago'));
    }
}
