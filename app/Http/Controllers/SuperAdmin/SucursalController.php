<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreSucursalRequest;
use App\Http\Requests\SuperAdmin\UpdateSucursalRequest;
use App\Models\Sucursal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SucursalController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', Sucursal::class);

        $sucursales = Sucursal::query()
            ->withCount(['usuarios', 'alumnos', 'instructores', 'carriles'])
            ->orderBy('nombre')
            ->get();

        return view('quantika.super-admin.sucursales.index', compact('sucursales'));
    }

    public function create(): View
    {
        $this->authorize('create', Sucursal::class);

        return view('quantika.super-admin.sucursales.create');
    }

    public function store(StoreSucursalRequest $request): RedirectResponse
    {
        $this->authorize('create', Sucursal::class);

        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $this->guardarLogo($request, $data['codigo']);
        }

        $sucursal = Sucursal::create($data);

        return redirect()
            ->route('super-admin.sucursales.show', $sucursal)
            ->with('status', 'Sucursal creada correctamente.');
    }

    public function show(Sucursal $sucursal): View
    {
        $this->authorize('view', $sucursal);

        $sucursal->loadCount(['usuarios', 'alumnos', 'instructores', 'carriles']);

        return view('quantika.super-admin.sucursales.show', compact('sucursal'));
    }

    /**
     * Ruta reservada `super-admin.sucursal-2`: muestra el detalle/edición
     * de la segunda sucursal usando la misma vista generalizada de `show`.
     */
    public function showSucursalDos(): View
    {
        $sucursal = Sucursal::where('codigo', 'SUC2')->first()
            ?? Sucursal::orderBy('id')->skip(1)->first()
            ?? Sucursal::orderBy('id')->firstOrFail();

        $this->authorize('view', $sucursal);

        $sucursal->loadCount(['usuarios', 'alumnos', 'instructores', 'carriles']);

        return view('quantika.super-admin.sucursales.show', compact('sucursal'));
    }

    public function update(UpdateSucursalRequest $request, Sucursal $sucursal): RedirectResponse
    {
        $this->authorize('update', $sucursal);

        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $this->guardarLogo($request, $data['codigo']);
        }

        $sucursal->update($data);

        return redirect()
            ->route('super-admin.sucursales.show', $sucursal)
            ->with('status', 'Sucursal actualizada correctamente.');
    }

    public function destroy(Sucursal $sucursal): RedirectResponse
    {
        $this->authorize('delete', $sucursal);

        $tieneDatos = $sucursal->usuarios()->exists()
            || $sucursal->alumnos()->exists()
            || $sucursal->instructores()->exists()
            || $sucursal->carriles()->exists()
            || $sucursal->horarios()->exists();

        if ($tieneDatos) {
            return redirect()
                ->route('super-admin.sucursales.index')
                ->withErrors(['sucursal' => 'No se puede eliminar la sucursal: tiene usuarios, alumnos, instructores u otros registros asociados.']);
        }

        $sucursal->delete();

        return redirect()
            ->route('super-admin.sucursales.index')
            ->with('status', 'Sucursal eliminada correctamente.');
    }

    private function guardarLogo(Request $request, string $codigo): string
    {
        $file = $request->file('logo');
        $nombre = Str::slug($codigo).'-'.time().'.'.$file->getClientOriginalExtension();

        $file->move(public_path('images/sucursales'), $nombre);

        return 'images/sucursales/'.$nombre;
    }
}
