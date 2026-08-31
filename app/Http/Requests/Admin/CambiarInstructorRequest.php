<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CambiarInstructorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('horario'));
    }

    public function rules(): array
    {
        return [
            'instructor_id' => ['required', 'integer', 'exists:instructores,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'instructor_id.required' => 'Selecciona el nuevo instructor para esta clase.',
        ];
    }
}
