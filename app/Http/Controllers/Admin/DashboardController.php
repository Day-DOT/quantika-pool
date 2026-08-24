<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EstadoAlumno;
use App\Enums\EstadoPago;
use App\Http\Controllers\Admin\Concerns\CalculaProgresoNivel;
use App\Http\Controllers\Admin\Concerns\ScopesSucursal;
use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Cita;
use App\Models\Horario;
use App\Models\Instructor;
use App\Models\Nivel;
use App\Models\Pago;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use CalculaProgresoNivel;
    use ScopesSucursal;

    public function index(): View
    {
        $sucursalId = $this->sucursalId();
        $hoy = Carbon::now();
        $diaSemanaHoy = $hoy->dayOfWeekIso; // 1 = lunes ... 7 = domingo

        // --- Alumnos activos ---
        $alumnosActivosQuery = Alumno::query()->where('estado', EstadoAlumno::Activo->value);
        $this->aplicarSucursal($alumnosActivosQuery);
        $alumnosActivos = $alumnosActivosQuery->count();

        $nuevosEsteMesQuery = Alumno::query()
            ->whereYear('fecha_inscripcion', $hoy->year)
            ->whereMonth('fecha_inscripcion', $hoy->month);
        $this->aplicarSucursal($nuevosEsteMesQuery);
        $alumnosNuevosMes = $nuevosEsteMesQuery->count();

        // --- Instructores ---
        $instructoresQuery = Instructor::query()->where('estado', 'activo');
        $this->aplicarSucursal($instructoresQuery);
        $instructoresActivos = $instructoresQuery->count();

        $enClaseAhoraQuery = Horario::query()
            ->where('activo', true)
            ->where('dia_semana', $diaSemanaHoy)
            ->where('hora_inicio', '<=', $hoy->format('H:i:s'))
            ->where('hora_fin', '>', $hoy->format('H:i:s'));
        $this->aplicarSucursal($enClaseAhoraQuery);
        $instructoresEnClase = $enClaseAhoraQuery->distinct()->count('instructor_id');
        $instructoresDisponibles = max($instructoresActivos - $instructoresEnClase, 0);

        // --- Clases de hoy ---
        $clasesHoyQuery = Horario::query()->where('activo', true)->where('dia_semana', $diaSemanaHoy);
        $this->aplicarSucursal($clasesHoyQuery);
        $clasesHoy = $clasesHoyQuery->count();

        $clasesEnCursoQuery = Horario::query()
            ->where('activo', true)
            ->where('dia_semana', $diaSemanaHoy)
            ->where('hora_inicio', '<=', $hoy->format('H:i:s'))
            ->where('hora_fin', '>', $hoy->format('H:i:s'));
        $this->aplicarSucursal($clasesEnCursoQuery);
        $clasesEnCurso = $clasesEnCursoQuery->count();

        // --- Asistencia (últimos 30 días) ---
        $citasBaseQuery = Cita::query()->whereNotNull('asistio')->where('fecha', '>=', $hoy->copy()->subDays(30));
        $this->aplicarSucursal($citasBaseQuery);
        $citasRegistradas = $citasBaseQuery->count();

        $citasAsistieronQuery = Cita::query()
            ->whereNotNull('asistio')
            ->where('asistio', true)
            ->where('fecha', '>=', $hoy->copy()->subDays(30));
        $this->aplicarSucursal($citasAsistieronQuery);
        $citasAsistieron = $citasAsistieronQuery->count();

        $asistenciaPct = $citasRegistradas > 0 ? round(($citasAsistieron / $citasRegistradas) * 100, 1) : 0.0;
        $asistenciaGrados = round(($asistenciaPct / 100) * 360, 0);

        // --- Ingresos del mes ---
        $ingresosMesQuery = Pago::query()
            ->where('estado', EstadoPago::Pagado->value)
            ->whereYear('fecha_pago', $hoy->year)
            ->whereMonth('fecha_pago', $hoy->month);
        $this->aplicarSucursal($ingresosMesQuery);
        $ingresosMes = (float) $ingresosMesQuery->sum('monto');

        $mesAnterior = $hoy->copy()->subMonthNoOverflow();
        $ingresosMesAnteriorQuery = Pago::query()
            ->where('estado', EstadoPago::Pagado->value)
            ->whereYear('fecha_pago', $mesAnterior->year)
            ->whereMonth('fecha_pago', $mesAnterior->month);
        $this->aplicarSucursal($ingresosMesAnteriorQuery);
        $ingresosMesAnterior = (float) $ingresosMesAnteriorQuery->sum('monto');

        $ingresosCambioPct = $ingresosMesAnterior > 0
            ? round((($ingresosMes - $ingresosMesAnterior) / $ingresosMesAnterior) * 100, 1)
            : null;

        // --- Pagos pendientes (situación de pagos) ---
        $pagosPendientesQuery = Pago::query()->where('estado', EstadoPago::Pendiente->value);
        $this->aplicarSucursal($pagosPendientesQuery);
        $pagosPendientesMonto = (float) (clone $pagosPendientesQuery)->sum('monto');
        $pagosPendientesCount = (clone $pagosPendientesQuery)->count();

        // --- Alertas de pagos próximos a vencer (siguientes 5 días) ---
        $pagosProximosQuery = Pago::proximosAVencer(5);
        $this->aplicarSucursal($pagosProximosQuery);
        $pagosProximosVencer = $pagosProximosQuery->with('alumno')
            ->orderBy('fecha_vencimiento')
            ->get()
            ->map(fn (Pago $pago) => [
                'pago' => $pago,
                'diasRestantes' => max(0, $hoy->diffInDays($pago->fecha_vencimiento, false)),
            ]);

        // --- Actividad semanal (citas por día, semana actual) ---
        $inicioSemana = $hoy->copy()->startOfWeek(Carbon::MONDAY);
        $diasSemana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        $actividadSemanal = collect();

        foreach ($diasSemana as $indice => $etiqueta) {
            $fecha = $inicioSemana->copy()->addDays($indice);
            $citasDelDiaQuery = Cita::query()->whereDate('fecha', $fecha->toDateString());
            $this->aplicarSucursal($citasDelDiaQuery);

            $actividadSemanal->push([
                'label' => $etiqueta,
                'total' => $citasDelDiaQuery->count(),
                'esHoy' => $fecha->isSameDay($hoy),
            ]);
        }

        $maxActividad = max(1, $actividadSemanal->max('total'));

        // --- Niveles destacados ---
        $nivelesPreview = Nivel::ordenados()->where('activo', true)->take(4)->get()->map(function (Nivel $nivel) use ($sucursalId) {
            $datos = $this->progresoDeNivel($nivel, $sucursalId);

            return [
                'nivel' => $nivel,
                'progreso' => $datos['progreso'],
            ];
        });

        // --- Alumnos recientes ---
        $alumnosRecientesQuery = Alumno::query()->with(['nivel', 'sucursal'])->latest('fecha_inscripcion');
        $this->aplicarSucursal($alumnosRecientesQuery);
        $alumnosRecientes = $alumnosRecientesQuery->take(5)->get()->map(function (Alumno $alumno) {
            $citasCompletadas = $alumno->citas()->whereNotNull('asistio')->count();
            $citasAsistidas = $alumno->citas()->where('asistio', true)->count();
            $asistenciaAlumno = $citasCompletadas > 0 ? round(($citasAsistidas / $citasCompletadas) * 100) : null;

            return [
                'alumno' => $alumno,
                'iniciales' => $this->iniciales($alumno->nombre, $alumno->apellidos),
                'asistencia' => $asistenciaAlumno,
            ];
        });

        return view('quantika.admin.dashboard', [
            'statAlumnosActivos' => $alumnosActivos,
            'statAlumnosNuevosMes' => $alumnosNuevosMes,
            'statInstructoresActivos' => $instructoresActivos,
            'statInstructoresDisponibles' => $instructoresDisponibles,
            'statClasesHoy' => $clasesHoy,
            'statClasesEnCurso' => $clasesEnCurso,
            'statAsistenciaPct' => $asistenciaPct,
            'statAsistenciaGrados' => $asistenciaGrados,
            'statIngresosMes' => $ingresosMes,
            'statIngresosCambioPct' => $ingresosCambioPct,
            'statPagosPendientesMonto' => $pagosPendientesMonto,
            'statPagosPendientesCount' => $pagosPendientesCount,
            'pagosProximosVencer' => $pagosProximosVencer,
            'actividadSemanal' => $actividadSemanal,
            'maxActividadSemanal' => $maxActividad,
            'nivelesPreview' => $nivelesPreview,
            'alumnosRecientes' => $alumnosRecientes,
        ]);
    }

    private function iniciales(string $nombre, string $apellidos): string
    {
        $inicial1 = mb_substr($nombre, 0, 1);
        $inicial2 = mb_substr($apellidos, 0, 1);

        return mb_strtoupper($inicial1.$inicial2);
    }
}
