<?php

namespace App\Http\Controllers\Instructor\Concerns;

use App\Models\Instructor;
use Illuminate\Http\Request;

/**
 * Un usuario autenticado con role=instructor puede, en teoría, no tener
 * todavía un perfil de instructor creado por un administrador. Este trait
 * centraliza la resolución de ese perfil para todos los controladores del
 * portal del instructor, de forma que ningún controlador olvide validarlo.
 */
trait ResuelveInstructor
{
    /**
     * Perfil de instructor del usuario autenticado. Aborta con 403 si el
     * usuario todavía no tiene un perfil de instructor asignado.
     */
    protected function instructorActivo(Request $request): Instructor
    {
        $instructor = $request->user()->instructor;

        abort_if(
            $instructor === null,
            403,
            'Tu perfil de instructor aún no ha sido configurado. Contacta a un administrador.'
        );

        return $instructor;
    }
}
