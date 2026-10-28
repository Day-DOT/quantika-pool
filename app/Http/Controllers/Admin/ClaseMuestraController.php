<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EstadoClaseMuestra;
use App\Http\Controllers\Admin\Concerns\ScopesSucursal;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClaseMuestraRequest;
use App\Models\ClaseMuestra;
use App\Models\Horario;
use App\Support\Reposiciones;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ClaseMuestraController extends Controller
{
    use ScopesSucursal;

    /**
     * Historial de clases muestra: las próximas y todas las que ya se
     * tomaron, con búsqueda por alumno, tutor, teléfono o correo.
     */
    public function index(Request $request): View
    {
        $busqueda = trim((string) $request->query('q', ''));

        $query = ClaseMuestra::query()
            ->with(['horario.carril', 'horario.instructor.user', 'sucursal'])
            ->orderByDesc('fecha')
            ->orderByDesc('hora_inicio');
        $this->aplicarSucursal($query);

        if ($busqueda !== '') {
            $telefono = ClaseMuestra::normalizarTelefono($busqueda);
            $nombre = ClaseMuestra::normalizarNombre($busqueda);

            $query->where(function ($q) use ($busqueda, $telefono, $nombre) {
                $q->where('nombre_normalizado', 'like', "%{$nombre}%")
                    ->orWhere('nombre_tutor', 'like', "%{$busqueda}%")
                    ->orWhere('email', 'like', '%'.mb_strtolower($busqueda).'%');

                if (strlen($telefono) >= 4) {
                    $q->orWhere('telefono_normalizado', 'like', "%{$telefono}%");
                }
            });
        }

        return view('quantika.clases-muestra.index', [
            'clases' => $query->paginate(25)->withQueryString(),
            'busqueda' => $busqueda,
        ]);
    }

    /**
     * Agenda una clase muestra en un grupo y fecha. Si la misma persona ya
     * tomó una (mismo teléfono, correo o nombre del alumno), se avisa y solo
     * se agenda si la administración lo confirma.
     */
    public function store(StoreClaseMuestraRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $horario = Horario::findOrFail($datos['horario_id']);
        $fecha = Carbon::parse($datos['fecha'])->startOfDay();
        $sucursalId = $this->sucursalId();

        if (! $horario->activo || ($sucursalId && $horario->sucursal_id !== $sucursalId)) {
            $this->rechazar('horario_id', 'El grupo debe estar activo y pertenecer a tu sucursal.');
        }

        if ($fecha->isoWeekday() !== $horario->dia_semana->value) {
            $this->rechazar('fecha', "La fecha elegida no coincide con el día ({$horario->dia_semana->label()}) del grupo seleccionado.");
        }

        if (Reposiciones::cupoDisponible($horario, $fecha) <= 0) {
            $this->rechazar('horario_id', 'Ese grupo ya no tiene cupo disponible para esa fecha.');
        }

        $anteriores = ClaseMuestra::deLaMismaPersona($datos['telefono'], $datos['email'] ?? null, $datos['nombre_alumno'])
            ->with('horario')
            ->orderByDesc('fecha')
            ->get();

        if ($anteriores->isNotEmpty() && ! $request->boolean('confirmar_repetida')) {
            $this->rechazar('clase_muestra_repetida', $this->avisoDeRepetida($anteriores->first(), $anteriores->count()));
        }

        $clase = ClaseMuestra::create([
            'sucursal_id' => $horario->sucursal_id,
            'horario_id' => $horario->id,
            'fecha' => $fecha->toDateString(),
            'hora_inicio' => $horario->hora_inicio,
            'hora_fin' => $horario->hora_fin,
            'nombre_alumno' => $datos['nombre_alumno'],
            'edad' => $datos['edad'],
            'nombre_tutor' => $datos['nombre_tutor'],
            'telefono' => $datos['telefono'],
            'email' => $datos['email'] ?? null,
            'estado' => EstadoClaseMuestra::Programada->value,
            'notas' => $datos['notas'] ?? null,
            'registrado_por' => $request->user()->id,
        ]);

        return back()->with(
            'status',
            "Clase muestra agendada para {$clase->descripcionCorta()} el {$fecha->format('d/m/Y')} en {$horario->nombre_grupo}."
        );
    }

    /**
     * Registra si asistió, si no asistió o cancela la clase muestra. Una
     * cancelada libera el lugar y no cuenta como clase muestra tomada.
     */
    public function actualizarEstado(Request $request, ClaseMuestra $claseMuestra): RedirectResponse
    {
        $sucursalId = $this->sucursalId();
        abort_if($sucursalId && $claseMuestra->sucursal_id !== $sucursalId, 403);

        $datos = $request->validate([
            'estado' => ['required', Rule::enum(EstadoClaseMuestra::class)],
        ]);

        $estado = EstadoClaseMuestra::from($datos['estado']);

        if ($estado !== EstadoClaseMuestra::Cancelada && $estado !== EstadoClaseMuestra::Programada && $claseMuestra->fecha->isFuture()) {
            return back()->withErrors(['clase_muestra' => 'La asistencia se registra el día de la clase o después.']);
        }

        // Al deshacer una cancelación vuelve a ocupar lugar: tiene que haber cupo.
        if ($claseMuestra->estado === EstadoClaseMuestra::Cancelada && $estado !== EstadoClaseMuestra::Cancelada
            && $claseMuestra->horario && Reposiciones::cupoDisponible($claseMuestra->horario, $claseMuestra->fecha) <= 0) {
            return back()->withErrors(['clase_muestra' => 'Ese grupo ya no tiene cupo para esa fecha.']);
        }

        $claseMuestra->update(['estado' => $estado->value]);

        return back()->with('status', "Clase muestra de {$claseMuestra->nombre_alumno}: {$estado->label()}.");
    }

    private function avisoDeRepetida(ClaseMuestra $anterior, int $total): string
    {
        $donde = $anterior->horario?->nombre_grupo ? " en {$anterior->horario->nombre_grupo}" : '';
        $veces = $total > 1 ? " Tiene {$total} clases muestra registradas." : '';

        return "Esta persona ya tomó anteriormente una clase muestra: {$anterior->descripcionCorta()}, "
            ."tutor {$anterior->nombre_tutor}, tel. {$anterior->telefono}, el {$anterior->fecha->format('d/m/Y')}{$donde} "
            ."({$anterior->estado->label()}).{$veces} La clase muestra es gratuita una sola vez; "
            .'si aun así quieres agendarla, marca «Agendar de todos modos».';
    }

    /**
     * @throws ValidationException
     */
    private function rechazar(string $campo, string $mensaje): never
    {
        throw ValidationException::withMessages([$campo => $mensaje]);
    }
}
