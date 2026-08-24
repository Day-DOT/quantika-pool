<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreNivelRequest;
use App\Http\Requests\SuperAdmin\UpdateNivelRequest;
use App\Models\Nivel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NivelController extends Controller
{
    use AuthorizesRequests;

    public function create(): View
    {
        $this->authorize('create', Nivel::class);

        return view('quantika.super-admin.niveles.create');
    }

    public function store(StoreNivelRequest $request): RedirectResponse
    {
        $this->authorize('create', Nivel::class);

        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $this->guardarImagen($request);
        }

        Nivel::create($data);

        return redirect()
            ->route('niveles.index')
            ->with('status', 'Nivel creado correctamente.');
    }

    public function edit(Nivel $nivel): View
    {
        $this->authorize('update', $nivel);

        return view('quantika.super-admin.niveles.edit', compact('nivel'));
    }

    public function update(UpdateNivelRequest $request, Nivel $nivel): RedirectResponse
    {
        $this->authorize('update', $nivel);

        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $this->guardarImagen($request);
        }

        $nivel->update($data);

        return redirect()
            ->route('niveles.index')
            ->with('status', 'Nivel actualizado correctamente.');
    }

    private function guardarImagen(Request $request): string
    {
        $file = $request->file('imagen');
        $nombre = Str::slug($request->input('nombre')).'-'.time().'.'.$file->getClientOriginalExtension();

        $file->move(public_path('images/Niveles'), $nombre);

        return 'images/Niveles/'.$nombre;
    }
}
