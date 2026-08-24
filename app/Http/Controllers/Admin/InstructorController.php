<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Rol;
use App\Http\Controllers\Admin\Concerns\ScopesSucursal;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInstructorRequest;
use App\Http\Requests\Admin\UpdateInstructorRequest;
use App\Models\Horario;
use App\Models\Instructor;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InstructorController extends Controller
{
    use AuthorizesRequests;
    use ScopesSucursal;

    public function index(): View
    {
        $this->authorize('viewAny', Instructor::class);

        $ahora = Carbon::now();
        $diaSemanaHoy = $ahora->dayOfWeekIso;

        $query = Instructor::query()->with(['user', 'sucursal']);
        $this->aplicarSucursal($query);

        $instructores = $query->get()->map(function (Instructor $instructor) use ($ahora, $diaSemanaHoy) {
            $enClaseAhora = Horario::query()
                ->where('instructor_id', $instructor->id)
                ->where('activo', true)
                ->where('dia_semana', $diaSemanaHoy)
                ->where('hora_inicio', '<=', $ahora->format('H:i:s'))
                ->where('hora_fin', '>', $ahora->format('H:i:s'))
                ->exists();

            return [
                'instructor' => $instructor,
                'iniciales' => $this->iniciales($instructor->user?->name ?? '??'),
                'enClase' => $enClaseAhora,
            ];
        });

        $sucursales = Sucursal::query()->orderBy('id')->get();
        $porSucursal = $sucursales->mapWithKeys(function (Sucursal $sucursal) use ($instructores) {
            return [$sucursal->id => $instructores->filter(fn ($fila) => $fila['instructor']->sucursal_id === $sucursal->id)->count()];
        });

        return view('quantika.instructores.index', [
            'instructores' => $instructores,
            'totalRegistrados' => $instructores->count(),
            'totalDisponibles' => $instructores->filter(fn ($fila) => ! $fila['enClase'] && $fila['instructor']->estado === 'activo')->count(),
            'sucursales' => $sucursales,
            'porSucursal' => $porSucursal,
            'sucursalesVisibles' => $this->sucursalesVisibles(),
            'esVistaGlobal' => $this->sucursalId() === null,
        ]);
    }

    public function store(StoreInstructorRequest $request): RedirectResponse
    {
        $this->authorize('create', Instructor::class);

        $datos = $request->validated();
        $sucursalId = $this->sucursalId() ?? (int) $datos['sucursal_id'];

        $passwordTemporal = Str::password(10, symbols: false);

        $instructor = DB::transaction(function () use ($datos, $sucursalId, $passwordTemporal) {
            $user = User::create([
                'name' => $datos['name'],
                'email' => $datos['email'],
                'password' => Hash::make($passwordTemporal),
                'role' => Rol::Instructor->value,
                'telefono' => $datos['telefono'] ?? null,
                'activo' => true,
            ]);

            return Instructor::create([
                'user_id' => $user->id,
                'sucursal_id' => $sucursalId,
                'especialidad' => $datos['especialidad'] ?? null,
                'estado' => 'activo',
            ]);
        });

        return redirect()->route('instructores.index')->with(
            'status',
            "Instructor {$datos['name']} registrado. Contraseña temporal: {$passwordTemporal}"
        );
    }

    public function update(UpdateInstructorRequest $request, Instructor $instructor): RedirectResponse
    {
        $datos = $request->validated();

        $instructor->update(['especialidad' => $datos['especialidad'] ?? null]);
        $instructor->user?->update([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'telefono' => $datos['telefono'] ?? null,
        ]);

        return redirect()->route('instructores.index')->with('status', 'Instructor actualizado correctamente.');
    }

    public function toggleEstado(Instructor $instructor): RedirectResponse
    {
        $this->authorize('update', $instructor);

        $nuevoEstado = $instructor->estado === 'activo' ? 'inactivo' : 'activo';
        $instructor->update(['estado' => $nuevoEstado]);

        if ($nuevoEstado === 'inactivo') {
            $instructor->user?->update(['activo' => false]);
        } else {
            $instructor->user?->update(['activo' => true]);
        }

        return back()->with('status', "Instructor {$instructor->user?->name}: estado actualizado a {$nuevoEstado}.");
    }

    private function iniciales(string $nombre): string
    {
        $partes = preg_split('/\s+/', trim($nombre));
        $primero = mb_substr($partes[0] ?? '', 0, 1);
        $segundo = mb_substr($partes[1] ?? '', 0, 1);

        return mb_strtoupper($primero.$segundo);
    }
}
