<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreCarrilRequest;
use App\Http\Requests\SuperAdmin\UpdateCarrilRequest;
use App\Models\Carril;
use App\Models\Sucursal;
use App\Support\SucursalContext;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarrilController extends Controller
{
    use AuthorizesRequests;

    /**
     * El único filtro de sucursal es el selector principal del topbar
     * (SucursalContext): si el super admin elige una sucursal ahí, esta
     * lista se reduce a los carriles de esa sucursal. No hay un filtro de
     * sucursal independiente en esta página.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Carril::class);

        $carriles = Carril::query()
            ->with('sucursal')
            ->withCount('horarios')
            ->when(SucursalContext::actualId(), fn ($q, $sucursalId) => $q->where('sucursal_id', $sucursalId))
            ->orderBy('sucursal_id')
            ->orderBy('nombre')
            ->get();

        return view('quantika.super-admin.carriles.index', [
            'carriles' => $carriles,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Carril::class);

        return view('quantika.super-admin.carriles.create', [
            'sucursales' => Sucursal::orderBy('nombre')->get(),
        ]);
    }

    public function store(StoreCarrilRequest $request): RedirectResponse
    {
        $this->authorize('create', Carril::class);

        Carril::create($request->validated());

        return redirect()
            ->route('super-admin.carriles.index')
            ->with('status', 'Carril creado correctamente.');
    }

    public function edit(Carril $carril): View
    {
        $this->authorize('update', $carril);

        return view('quantika.super-admin.carriles.edit', [
            'carril' => $carril,
            'sucursales' => Sucursal::orderBy('nombre')->get(),
        ]);
    }

    public function update(UpdateCarrilRequest $request, Carril $carril): RedirectResponse
    {
        $this->authorize('update', $carril);

        $carril->update($request->validated());

        return redirect()
            ->route('super-admin.carriles.index')
            ->with('status', 'Carril actualizado correctamente.');
    }

    public function destroy(Carril $carril): RedirectResponse
    {
        $this->authorize('delete', $carril);

        if ($carril->horarios()->exists()) {
            return back()->withErrors(['carril' => 'No se puede eliminar el carril: tiene horarios de clase asignados.']);
        }

        $carril->delete();

        return redirect()
            ->route('super-admin.carriles.index')
            ->with('status', 'Carril eliminado correctamente.');
    }
}
