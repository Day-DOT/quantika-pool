<?php

namespace App\Http\Controllers;

use App\Enums\EstadoCita;
use App\Models\Alumno;
use App\Models\Cita;
use App\Models\Horario;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AsistenciaQrController extends Controller
{
    /**
     * Página con el lector de código QR (usa la cámara del dispositivo)
     * para que el staff registre asistencia escaneando el código del
     * alumno.
     */
    public function escanear(): View
    {
        return view('quantika.asistencia.escanear');
    }

    /**
     * Se abre al escanear el código QR de un alumno (o al entrar
     * directamente a la URL que codifica). Registra su asistencia en cada
     * grupo que tenga programado hoy y para el que quien escanea tenga
     * permiso de gestionar.
     */
    public function registrar(Request $request, string $token): View
    {
        $alumno = Alumno::where('qr_token', $token)->first();

        if (! $alumno) {
            return view('quantika.asistencia.resultado', [
                'alumno' => null,
                'exito' => false,
                'mensaje' => 'Este código QR no es válido.',
            ]);
        }

        $hoy = today();
        $diaSemanaHoy = $hoy->dayOfWeekIso;

        $horariosHoy = $alumno->inscripciones()
            ->activas()
            ->with('horario')
            ->get()
            ->pluck('horario')
            ->filter(fn (?Horario $horario) => $horario && $horario->activo && $horario->dia_semana->value === $diaSemanaHoy)
            ->unique('id');

        if ($horariosHoy->isEmpty()) {
            return view('quantika.asistencia.resultado', [
                'alumno' => $alumno,
                'exito' => false,
                'mensaje' => 'Este alumno no tiene clase programada para hoy.',
            ]);
        }

        $gruposRegistrados = collect();

        foreach ($horariosHoy as $horario) {
            $cita = Cita::where('horario_id', $horario->id)
                ->where('alumno_id', $alumno->id)
                ->whereDate('fecha', $hoy)
                ->first();

            if (! $cita) {
                $cita = new Cita([
                    'horario_id' => $horario->id,
                    'alumno_id' => $alumno->id,
                    'sucursal_id' => $horario->sucursal_id,
                    'fecha' => $hoy,
                    'hora_inicio' => $horario->hora_inicio,
                    'hora_fin' => $horario->hora_fin,
                ]);
            }

            if ($request->user()->cannot('update', $cita)) {
                continue;
            }

            $cita->asistio = true;
            $cita->estado = EstadoCita::Completada;
            $cita->registrado_por = $request->user()->id;
            $cita->save();

            $gruposRegistrados->push($horario->nombre_grupo);
        }

        if ($gruposRegistrados->isEmpty()) {
            return view('quantika.asistencia.resultado', [
                'alumno' => $alumno,
                'exito' => false,
                'mensaje' => 'No tienes permiso para registrar la asistencia de este alumno.',
            ]);
        }

        return view('quantika.asistencia.resultado', [
            'alumno' => $alumno,
            'exito' => true,
            'mensaje' => 'Asistencia registrada en: '.$gruposRegistrados->implode(', ').'.',
        ]);
    }
}
