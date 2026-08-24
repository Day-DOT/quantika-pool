<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\EstadoAlumno;
use App\Enums\EstadoPago;
use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Cita;
use App\Models\Instructor;
use App\Models\Nivel;
use App\Models\Pago;
use App\Models\Sucursal;
use App\Support\SucursalContext;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $sucursales = Sucursal::orderBy('nombre')->get();
        $sucursalId = SucursalContext::actualId();
        $esGlobal = SucursalContext::esVistaGlobal();

        $statsPorSucursal = $sucursales->mapWithKeys(
            fn (Sucursal $sucursal) => [$sucursal->id => $this->calcularStats($sucursal->id)]
        );

        $stats = $sucursalId
            ? ($statsPorSucursal[$sucursalId] ?? $this->calcularStats($sucursalId))
            : $this->calcularStats(null);

        return view('quantika.super-admin.dashboard', [
            'sucursales' => $sucursales,
            'sucursalActual' => $sucursalId ? $sucursales->firstWhere('id', $sucursalId) : null,
            'esGlobal' => $esGlobal,
            'stats' => $stats,
            'statsPorSucursal' => $statsPorSucursal,
            'niveles' => Nivel::ordenados()->get(),
            'alumnosRecientes' => $this->alumnosRecientes($sucursalId),
        ]);
    }

    private function calcularStats(?int $sucursalId): array
    {
        $alumnos = Alumno::query()->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId));
        $pagos = Pago::query()->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId));
        $instructores = Instructor::query()->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId));
        $citasHoy = Cita::query()
            ->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('fecha', now());

        $pagosPorEstado = Pago::query()
            ->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId))
            ->selectRaw('estado, count(*) as total, coalesce(sum(monto),0) as monto')
            ->groupBy('estado')
            ->get()
            ->keyBy('estado');

        $porEstado = fn (EstadoPago $estado) => $pagosPorEstado->get($estado->value);

        return [
            'alumnos_activos' => (clone $alumnos)->where('estado', EstadoAlumno::Activo->value)->count(),
            'alumnos_total' => (clone $alumnos)->count(),
            'instructores' => (clone $instructores)->count(),
            'citas_hoy' => $citasHoy->count(),
            'ingresos_mes' => (clone $pagos)
                ->where('estado', EstadoPago::Pagado->value)
                ->whereNotNull('fecha_pago')
                ->whereMonth('fecha_pago', now()->month)
                ->whereYear('fecha_pago', now()->year)
                ->sum('monto'),
            'pagos_pendientes' => (int) ($porEstado(EstadoPago::Pendiente)?->total ?? 0),
            'pagos_pagados' => (int) ($porEstado(EstadoPago::Pagado)?->total ?? 0),
            'pagos_en_revision' => (int) ($porEstado(EstadoPago::EnRevision)?->total ?? 0),
            'pagos_vencidos' => (int) ($porEstado(EstadoPago::Vencido)?->total ?? 0),
            'monto_pendiente' => (float) ($porEstado(EstadoPago::Pendiente)?->monto ?? 0),
            'monto_vencido' => (float) ($porEstado(EstadoPago::Vencido)?->monto ?? 0),
        ];
    }

    private function alumnosRecientes(?int $sucursalId): Collection
    {
        return Alumno::query()
            ->with(['sucursal', 'nivel'])
            ->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId))
            ->latest('fecha_inscripcion')
            ->limit(6)
            ->get();
    }
}
