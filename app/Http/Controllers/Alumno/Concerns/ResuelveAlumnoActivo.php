<?php

namespace App\Http\Controllers\Alumno\Concerns;

use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * El tutor autenticado puede tener varios alumnos (hijos) vinculados.
 * Este trait centraliza la lógica para listar sus alumnos y resolver
 * cuál de ellos está "activo" en la pantalla actual del portal,
 * recordando la elección entre páginas mediante la sesión.
 */
trait ResuelveAlumnoActivo
{
    /**
     * Todos los alumnos (hijos) del tutor autenticado.
     *
     * @return Collection<int, Alumno>
     */
    protected function alumnosDelTutor(Request $request): Collection
    {
        return $request->user()
            ->alumnos()
            ->with(['nivel', 'sucursal'])
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Resuelve el alumno seleccionado para esta petición: por query string
     * (?alumno=), por la última elección guardada en sesión, o el primero
     * de la lista. Siempre verificado contra la política de autorización.
     */
    protected function alumnoActivo(Request $request, Collection $alumnos): ?Alumno
    {
        if ($alumnos->isEmpty()) {
            return null;
        }

        $solicitadoId = $request->query('alumno');

        $alumno = $solicitadoId !== null
            ? $alumnos->firstWhere('id', (int) $solicitadoId)
            : null;

        $alumno ??= $alumnos->firstWhere('id', (int) session('portal_alumno_id'));

        $alumno ??= $alumnos->first();

        $this->authorize('view', $alumno);

        session(['portal_alumno_id' => $alumno->id]);

        return $alumno;
    }
}
