<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionSistema;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConfiguracionController extends Controller
{
    public function index(): View
    {
        return view('quantika.configuracion.index', [
            'categoriasEdad' => ConfiguracionSistema::categoriasEdad(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'categoria_bebes' => ['required', 'string', 'max:100'],
            'categoria_ninos' => ['required', 'string', 'max:100'],
            'categoria_adultos' => ['required', 'string', 'max:100'],
        ]);

        foreach ($datos as $clave => $valor) {
            ConfiguracionSistema::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
        }

        return back()->with('status', 'Nombres de divisiones actualizados correctamente.');
    }
}
