<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\Rol;
use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreUsuarioRequest;
use App\Http\Requests\SuperAdmin\UpdateUsuarioRequest;
use App\Models\Instructor;
use App\Models\Sucursal;
use App\Models\User;
use App\Support\SucursalContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    /**
     * Gestión de TODOS los usuarios del sistema (los 4 roles), incluida la
     * asignación de rol y sucursal base. Nombre de ruta expuesto para que
     * otros módulos (p. ej. Configuración del Admin) puedan enlazarlo:
     * `super-admin.usuarios.index`.
     *
     * El único filtro de sucursal es el selector principal del topbar
     * (SucursalContext): si el super admin elige una sucursal ahí, esta
     * lista se reduce a los admins/instructores de esa sucursal (los roles
     * que no pertenecen a ninguna sucursal, super admin y tutor, siempre se
     * muestran). No hay un filtro de sucursal independiente en esta página.
     */
    public function index(Request $request): View
    {
        $sucursalActualId = SucursalContext::actualId();

        $usuarios = User::query()
            ->with(['sucursal', 'instructor.sucursal'])
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->string('role')))
            ->when($sucursalActualId, function ($q) use ($sucursalActualId) {
                $q->where(function ($sub) use ($sucursalActualId) {
                    $sub->where('sucursal_id', $sucursalActualId)
                        ->orWhereHas('instructor', fn ($i) => $i->where('sucursal_id', $sucursalActualId))
                        ->orWhereIn('role', [Rol::SuperAdmin->value, Rol::Alumno->value]);
                });
            })
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $buscar = $request->string('buscar');
                $q->where(fn ($sub) => $sub->where('name', 'like', "%{$buscar}%")->orWhere('email', 'like', "%{$buscar}%"));
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('quantika.super-admin.usuarios.index', [
            'usuarios' => $usuarios,
            'roles' => Rol::cases(),
            'filtros' => $request->only(['role', 'buscar']),
        ]);
    }

    public function create(): View
    {
        return view('quantika.super-admin.usuarios.create', [
            'sucursales' => Sucursal::orderBy('nombre')->get(),
            'roles' => Rol::cases(),
        ]);
    }

    public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $rol = Rol::from($data['role']);

        $usuario = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $rol->value,
            'sucursal_id' => $rol === Rol::Admin ? $data['sucursal_id'] : null,
            'telefono' => $data['telefono'] ?? null,
            'activo' => $data['activo'],
        ]);

        $this->sincronizarInstructor($usuario, $rol, $data);

        return redirect()
            ->route('super-admin.usuarios.index')
            ->with('status', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario): View
    {
        $usuario->load('instructor');

        return view('quantika.super-admin.usuarios.edit', [
            'usuario' => $usuario,
            'sucursales' => Sucursal::orderBy('nombre')->get(),
            'roles' => Rol::cases(),
        ]);
    }

    public function update(UpdateUsuarioRequest $request, User $usuario): RedirectResponse
    {
        $data = $request->validated();
        $rol = Rol::from($data['role']);

        $usuario->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $rol->value,
            'sucursal_id' => $rol === Rol::Admin ? $data['sucursal_id'] : null,
            'telefono' => $data['telefono'] ?? null,
            'activo' => $data['activo'],
        ]);

        if (! empty($data['password'])) {
            $usuario->password = Hash::make($data['password']);
        }

        $usuario->save();

        $this->sincronizarInstructor($usuario, $rol, $data);

        return redirect()
            ->route('super-admin.usuarios.index')
            ->with('status', 'Usuario actualizado correctamente.');
    }

    /**
     * Activa o desactiva el acceso de un usuario al sistema.
     */
    public function estado(User $usuario): RedirectResponse
    {
        if ($usuario->id === auth()->id()) {
            return back()->withErrors(['usuario' => 'No puedes desactivar tu propia cuenta.']);
        }

        $usuario->update(['activo' => ! $usuario->activo]);

        if ($usuario->role === Rol::Instructor) {
            $usuario->instructor?->update(['estado' => $usuario->activo ? 'activo' : 'inactivo']);
        }

        return back()->with('status', $usuario->activo ? 'Usuario activado.' : 'Usuario desactivado.');
    }

    private function sincronizarInstructor(User $usuario, Rol $rol, array $data): void
    {
        if ($rol === Rol::Instructor) {
            Instructor::updateOrCreate(
                ['user_id' => $usuario->id],
                [
                    'sucursal_id' => $data['sucursal_id'],
                    'especialidad' => $data['especialidad'] ?? null,
                    'estado' => $data['activo'] ? 'activo' : 'inactivo',
                ],
            );

            return;
        }

        Instructor::where('user_id', $usuario->id)->delete();
    }
}
