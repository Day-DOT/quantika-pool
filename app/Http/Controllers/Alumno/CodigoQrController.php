<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Alumno\Concerns\ResuelveAlumnoActivo;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CodigoQrController extends Controller
{
    use AuthorizesRequests;
    use ResuelveAlumnoActivo;

    /**
     * Código QR de asistencia del alumno activo: el tutor lo muestra desde
     * su celular para que el staff lo escanee al llegar a la alberca.
     */
    public function index(Request $request): View
    {
        $alumnos = $this->alumnosDelTutor($request);
        $alumno = $this->alumnoActivo($request, $alumnos);

        return view('quantika.portal.qr', [
            'alumnos' => $alumnos,
            'alumno' => $alumno,
        ]);
    }
}
