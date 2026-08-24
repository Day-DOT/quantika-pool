<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesSucursal;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarrilRequest;
use App\Http\Requests\Admin\UpdateCarrilRequest;
use App\Models\Carril;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CarrilController extends Controller
{
    use AuthorizesRequests;
    use ScopesSucursal;

    public function index(): View
    {
        $this->authorize('viewAny', Carril::class);

        $query = Carril::query()->withCount(['horarios' => fn ($q) => $q->where('activo', true)])->with('sucursal');
        $this->aplicarSucursal($query);

        return view('quantika.configuracion.carriles', [
            'carriles' => $query->orderBy('sucursal_id')->orderBy('nombre')->get(),
            'sucursales' => $this->sucursalesVisibles(),
            'esVistaGlobal' => $this->sucursalId() === null,
        ]);
    }

    public function store(StoreCarrilRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $sucursalId = $this->sucursalId() ?? (int) $datos['sucursal_id'];

        Carril::create([
            'sucursal_id' => $sucursalId,
            'nombre' => $datos['nombre'],
            'capacidad_maxima' => $datos['capacidad_maxima'],
            'activo' => true,
        ]);

        return redirect()->route('carriles.index')->with('status', "Carril \"{$datos['nombre']}\" creado correctamente.");
    }

    public function update(UpdateCarrilRequest $request, Carril $carril): RedirectResponse
    {
        $datos = $request->validated();

        $carril->update([
            'nombre' => $datos['nombre'],
            'capacidad_maxima' => $datos['capacidad_maxima'],
            'activo' => $request->boolean('activo', $carril->activo),
        ]);

        return redirect()->route('carriles.index')->with('status', "Carril \"{$carril->nombre}\" actualizado correctamente.");
    }

    public function destroy(Carril $carril): RedirectResponse
    {
        $this->authorize('delete', $carril);

        if ($carril->horarios()->where('activo', true)->exists()) {
            $carril->update(['activo' => false]);

            return redirect()->route('carriles.index')->with('status', "El carril \"{$carril->nombre}\" tiene clases activas; se desactivó en lugar de eliminarse.");
        }

        $carril->delete();

        return redirect()->route('carriles.index')->with('status', "Carril \"{$carril->nombre}\" eliminado.");
    }
}
