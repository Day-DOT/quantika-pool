<?php

namespace App\Http\Controllers;

use App\Enums\ConceptoPago;
use App\Enums\EstadoCita;
use App\Enums\EstadoPago;
use App\Models\Alumno;
use App\Models\Cita;
use App\Models\Pago;
use App\Support\ClasesDelAlumno;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AsistenciaQrController extends Controller
{
    use AuthorizesRequests;

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
    public function registrar(string $token): View
    {
        $alumno = Alumno::where('qr_token', $token)->first();

        if (! $alumno) {
            return view('quantika.asistencia.resultado', [
                'alumno' => null,
                'horariosHoy' => collect(),
                'exito' => false,
                'mensaje' => 'Este código QR no es válido.',
                'puedeRegistrar' => false,
            ]);
        }

        $this->authorize('view', $alumno);
        $alumno->load(['nivel', 'sucursal', 'tutorUser']);
        $horariosHoy = $this->horariosDeHoy($alumno);

        return view('quantika.asistencia.resultado', [
            'estadoPago' => $this->estadoDePago($alumno),
            'alumno' => $alumno,
            'horariosHoy' => $horariosHoy,
            'exito' => false,
            'mensaje' => $horariosHoy->isEmpty()
                ? 'Este alumno no tiene clase programada para hoy.'
                : 'Verifica los datos del alumno y confirma el registro de asistencia.',
            'puedeRegistrar' => $horariosHoy->isNotEmpty(),
        ]);
    }

    public function confirmar(Request $request, string $token): View
    {
        $alumno = Alumno::where('qr_token', $token)->first();

        if (! $alumno) {
            return view('quantika.asistencia.resultado', [
                'alumno' => null,
                'horariosHoy' => collect(),
                'exito' => false,
                'mensaje' => 'Este código QR no es válido.',
                'puedeRegistrar' => false,
            ]);
        }

        $this->authorize('view', $alumno);
        $horariosHoy = $this->horariosDeHoy($alumno);
        $gruposRegistrados = collect();

        foreach ($horariosHoy as $horario) {
            $cita = Cita::where('horario_id', $horario->id)
                ->where('alumno_id', $alumno->id)
                ->whereDate('fecha', today())
                ->first() ?? new Cita([
                    'horario_id' => $horario->id,
                    'alumno_id' => $alumno->id,
                    'sucursal_id' => $horario->sucursal_id,
                    'fecha' => today(),
                    'hora_inicio' => $horario->hora_inicio,
                    'hora_fin' => $horario->hora_fin,
                ]);

            if ($request->user()->cannot('update', $cita)
                || ($cita->exists && $cita->estado === EstadoCita::Cancelada)) {
                continue;
            }

            $cita->asistio = true;
            $cita->falta_avisada = false;
            $cita->estado = EstadoCita::Completada;
            $cita->registrado_por = $request->user()->id;
            $cita->save();
            $gruposRegistrados->push($horario->nombre_grupo);
        }

        $alumno->load(['nivel', 'sucursal', 'tutorUser']);

        return view('quantika.asistencia.resultado', [
            'estadoPago' => $this->estadoDePago($alumno),
            'alumno' => $alumno,
            'horariosHoy' => $horariosHoy,
            'exito' => $gruposRegistrados->isNotEmpty(),
            'mensaje' => $gruposRegistrados->isNotEmpty()
                ? 'Asistencia registrada en: '.$gruposRegistrados->implode(', ').'.'
                : ($horariosHoy->isEmpty()
                    ? 'Este alumno no tiene clase programada para hoy.'
                    : 'No tienes permiso para registrar la asistencia de este alumno.'),
            'puedeRegistrar' => false,
        ]);
    }

    /**
     * Grupos en los que el alumno tiene clase hoy: los de su inscripción
     * que caen en este día (salvo que esa clase se haya movido a otra
     * fecha) y los de una sola fecha: extra, reposición o reagendada.
     */
    private function horariosDeHoy(Alumno $alumno)
    {
        return ClasesDelAlumno::enRango($alumno, today(), today())
            ->map(fn (array $clase) => $clase['cita']->horario)
            ->filter()
            ->unique('id')
            ->values();
    }

    /**
     * Situación de sus pagos para avisar al escanear. La anualidad y la
     * mensualidad se revisan por separado, para decir cuál está vencida
     * (con desde cuándo y cuánto debe), cuál vence pronto o si está al
     * corriente.
     *
     * @return array{tipo: string, mensaje: string}
     */
    private function estadoDePago(Alumno $alumno): array
    {
        $vencidos = $alumno->pagos()->vencidos()->orderBy('fecha_vencimiento')->get()
            ->groupBy(fn (Pago $pago) => $pago->concepto->value);

        $avisos = collect([
            ConceptoPago::Inscripcion->value => ['Anualidad vencida', 'Anualidades vencidas'],
            ConceptoPago::Mensualidad->value => ['Mensualidad vencida', 'mensualidades vencidas'],
            ConceptoPago::Otro->value => ['Otro pago vencido', 'otros pagos vencidos'],
        ])
            ->filter(fn ($etiquetas, string $concepto) => $vencidos->has($concepto))
            ->map(function (array $etiquetas, string $concepto) use ($vencidos) {
                $delConcepto = $vencidos[$concepto];
                $titulo = $delConcepto->count() === 1 ? $etiquetas[0] : $delConcepto->count().' '.mb_strtolower($etiquetas[1]);

                return mb_strtoupper(mb_substr($titulo, 0, 1)).mb_substr($titulo, 1)
                    .' desde el '.$delConcepto->first()->fecha_vencimiento->format('d/m/Y')
                    .' · debe $'.number_format((float) $delConcepto->sum('monto'), 2);
            });

        if ($avisos->isNotEmpty()) {
            return ['tipo' => 'vencido', 'mensaje' => $avisos->implode(' | ')];
        }

        $proximo = $alumno->pagos()
            ->where('estado', EstadoPago::Pendiente->value)
            ->whereDate('fecha_vencimiento', '>=', today())
            ->whereDate('fecha_vencimiento', '<=', today()->addDays(5))
            ->orderBy('fecha_vencimiento')
            ->first();

        if ($proximo) {
            $que = match ($proximo->concepto) {
                ConceptoPago::Inscripcion => 'Su anualidad',
                ConceptoPago::Mensualidad => 'Su mensualidad',
                default => 'Un pago',
            };

            return [
                'tipo' => 'por_vencer',
                'mensaje' => "{$que} vence el ".$proximo->fecha_vencimiento->format('d/m/Y').' ($'.number_format((float) $proximo->monto, 2).')',
            ];
        }

        return ['tipo' => 'al_corriente', 'mensaje' => 'Pagos al corriente'];
    }
}
