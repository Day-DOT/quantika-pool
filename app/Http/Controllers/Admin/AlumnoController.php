<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EstadoAlumno;
use App\Enums\Rol;
use App\Http\Controllers\Admin\Concerns\ScopesSucursal;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAlumnoRequest;
use App\Http\Requests\Admin\UpdateAlumnoRequest;
use App\Models\Alumno;
use App\Models\AlumnoNivelHistorial;
use App\Models\Nivel;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AlumnoController extends Controller
{
    use AuthorizesRequests;
    use ScopesSucursal;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Alumno::class);

        $query = Alumno::query()->with(['nivel', 'sucursal', 'tutorUser']);
        $this->aplicarSucursal($query);

        // El buscador y los selects de nivel/sucursal/estado filtran en el
        // cliente (en tiempo real, sin recargar) sobre este listado completo.
        $alumnos = $query->orderBy('nombre')->get()->map(function (Alumno $alumno) {
            $citasCompletadas = $alumno->citas()->whereNotNull('asistio')->count();
            $citasAsistidas = $alumno->citas()->where('asistio', true)->count();

            return [
                'alumno' => $alumno,
                'iniciales' => $this->iniciales($alumno->nombre, $alumno->apellidos),
                'asistencia' => $citasCompletadas > 0 ? round(($citasAsistidas / $citasCompletadas) * 100) : null,
            ];
        });

        $totalRegistrados = $alumnos->count();
        $totalActivos = $alumnos->filter(fn ($fila) => $fila['alumno']->estado === EstadoAlumno::Activo)->count();
        $totalPrincipiantes = $alumnos->filter(fn ($fila) => $fila['alumno']->nivel?->categoria === 'Principiante')->count();
        $totalAvanzados = $alumnos->filter(fn ($fila) => $fila['alumno']->nivel?->categoria === 'Avanzado')->count();

        return view('quantika.alumnos.index', [
            'alumnos' => $alumnos,
            'niveles' => Nivel::ordenados()->get(),
            'sucursales' => $this->sucursalesVisibles(),
            'esVistaGlobal' => $this->sucursalId() === null,
            'totalRegistrados' => $totalRegistrados,
            'totalActivos' => $totalActivos,
            'totalPrincipiantes' => $totalPrincipiantes,
            'totalAvanzados' => $totalAvanzados,
            'abrirModalCrear' => $request->boolean('crear'),
        ]);
    }

    public function create(): RedirectResponse
    {
        $this->authorize('create', Alumno::class);

        // El flujo real de alta vive en el modal de "alumnos.index" (tiene más
        // campos que esta página histórica), así que redirigimos ahí con la
        // bandera para abrirlo automáticamente.
        return redirect()->route('alumnos.index', ['crear' => 1]);
    }

    public function store(StoreAlumnoRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        $sucursalId = $this->sucursalId() ?? (int) $datos['sucursal_id'];

        $resultado = DB::transaction(function () use ($datos, $sucursalId) {
            $tutor = User::where('email', $datos['tutor_email'])->first();
            $passwordTemporal = null;

            if (! $tutor) {
                $passwordTemporal = Str::password(10, symbols: false);

                $tutor = User::create([
                    'name' => $datos['tutor_nombre'],
                    'email' => $datos['tutor_email'],
                    'password' => Hash::make($passwordTemporal),
                    'role' => Rol::Alumno->value,
                    'telefono' => $datos['tutor_telefono'] ?? null,
                    'activo' => true,
                ]);
            } elseif (empty($tutor->telefono) && ! empty($datos['tutor_telefono'])) {
                $tutor->update(['telefono' => $datos['tutor_telefono']]);
            }

            $alumno = Alumno::create([
                'tutor_user_id' => $tutor->id,
                'sucursal_id' => $sucursalId,
                'nivel_id' => $datos['nivel_id'] ?? null,
                'nombre' => $datos['nombre'],
                'apellidos' => $datos['apellidos'],
                'fecha_nacimiento' => $datos['fecha_nacimiento'],
                'telefono' => $datos['telefono'] ?? null,
                'email' => $datos['email'] ?? null,
                'observaciones' => $datos['observaciones'] ?? null,
                'estado' => EstadoAlumno::Activo->value,
                'fecha_inscripcion' => now()->toDateString(),
            ]);

            if ($alumno->nivel_id) {
                AlumnoNivelHistorial::create([
                    'alumno_id' => $alumno->id,
                    'nivel_id' => $alumno->nivel_id,
                    'fecha_inicio' => $alumno->fecha_inscripcion,
                    'fecha_fin' => null,
                    'promovido_por' => auth()->id(),
                    'observaciones' => 'Inscripción inicial.',
                ]);
            }

            return ['alumno' => $alumno, 'passwordTemporal' => $passwordTemporal, 'tutor' => $tutor];
        });

        $mensaje = "Alumno {$resultado['alumno']->nombreCompleto()} registrado correctamente.";

        if ($resultado['passwordTemporal']) {
            $tutorEmail = $resultado['tutor']->email;
            $mensaje .= " Se creó una cuenta para el tutor ({$tutorEmail}) con contraseña temporal: {$resultado['passwordTemporal']}";
        }

        return redirect()->route('alumnos.show', $resultado['alumno'])->with('status', $mensaje);
    }

    public function show(Alumno $alumno): View
    {
        $this->authorize('view', $alumno);

        $alumno->load([
            'nivel',
            'sucursal',
            'tutorUser',
            'historialNiveles' => fn ($q) => $q->with('nivel')->orderByDesc('fecha_inicio'),
            'inscripciones' => fn ($q) => $q->where('activa', true)->with('horario.instructor.user', 'horario.carril'),
            'evaluaciones' => fn ($q) => $q->with('instructor.user', 'nivel')->orderByDesc('fecha'),
            'pagos' => fn ($q) => $q->orderByDesc('fecha_vencimiento'),
        ]);

        $citasCompletadas = $alumno->citas()->whereNotNull('asistio')->count();
        $citasAsistidas = $alumno->citas()->where('asistio', true)->count();
        $asistenciaPct = $citasCompletadas > 0 ? round(($citasAsistidas / $citasCompletadas) * 100) : null;

        $ultimaEvaluacion = $alumno->evaluaciones->first();

        return view('quantika.alumnos.show', [
            'alumno' => $alumno,
            'iniciales' => $this->iniciales($alumno->nombre, $alumno->apellidos),
            'asistenciaPct' => $asistenciaPct,
            'citasCompletadas' => $citasCompletadas,
            'citasAsistidas' => $citasAsistidas,
            'progresoNivel' => $ultimaEvaluacion?->porcentajeAvance() ?? 0,
        ]);
    }

    public function edit(Alumno $alumno): View
    {
        $this->authorize('update', $alumno);

        $alumno->load('tutorUser', 'nivel', 'sucursal');

        return view('quantika.alumnos.edit', [
            'alumno' => $alumno,
            'niveles' => Nivel::ordenados()->get(),
            'estados' => EstadoAlumno::cases(),
        ]);
    }

    public function update(UpdateAlumnoRequest $request, Alumno $alumno): RedirectResponse
    {
        $datos = $request->validated();

        DB::transaction(function () use ($datos, $alumno) {
            $nivelAnterior = $alumno->nivel_id;

            $alumno->update([
                'nombre' => $datos['nombre'],
                'apellidos' => $datos['apellidos'],
                'fecha_nacimiento' => $datos['fecha_nacimiento'],
                'telefono' => $datos['telefono'] ?? null,
                'email' => $datos['email'] ?? null,
                'observaciones' => $datos['observaciones'] ?? null,
                'estado' => $datos['estado'],
                'nivel_id' => $datos['nivel_id'] ?? null,
            ]);

            if ($alumno->tutorUser) {
                $alumno->tutorUser->update([
                    'name' => $datos['tutor_nombre'],
                    'email' => $datos['tutor_email'],
                    'telefono' => $datos['tutor_telefono'] ?? $alumno->tutorUser->telefono,
                ]);
            }

            if ($nivelAnterior !== $alumno->nivel_id) {
                AlumnoNivelHistorial::where('alumno_id', $alumno->id)
                    ->whereNull('fecha_fin')
                    ->update(['fecha_fin' => now()->toDateString()]);

                if ($alumno->nivel_id) {
                    AlumnoNivelHistorial::create([
                        'alumno_id' => $alumno->id,
                        'nivel_id' => $alumno->nivel_id,
                        'fecha_inicio' => now()->toDateString(),
                        'fecha_fin' => null,
                        'promovido_por' => auth()->id(),
                        'observaciones' => 'Actualizado desde la edición del alumno.',
                    ]);
                }
            }
        });

        return redirect()->route('alumnos.show', $alumno)->with('status', 'Alumno actualizado correctamente.');
    }

    public function baja(Request $request, Alumno $alumno): RedirectResponse
    {
        $this->authorize('delete', $alumno);

        $tipo = $request->string('tipo')->value() === 'definitiva'
            ? EstadoAlumno::BajaDefinitiva
            : EstadoAlumno::BajaTemporal;

        $alumno->update(['estado' => $tipo->value]);

        return back()->with('status', "Alumno {$alumno->nombreCompleto()} dado de baja ({$tipo->label()}).");
    }

    public function reactivar(Alumno $alumno): RedirectResponse
    {
        $this->authorize('update', $alumno);

        $alumno->update(['estado' => EstadoAlumno::Activo->value]);

        return back()->with('status', "Alumno {$alumno->nombreCompleto()} reactivado.");
    }

    private function iniciales(string $nombre, string $apellidos): string
    {
        return mb_strtoupper(mb_substr($nombre, 0, 1).mb_substr($apellidos, 0, 1));
    }
}
