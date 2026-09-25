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
            'categoria_bebes' => ['sometimes', 'string', 'max:100'],
            'categoria_ninos' => ['sometimes', 'string', 'max:100'],
            'categoria_adultos' => ['sometimes', 'string', 'max:100'],
        ]);

        foreach ($datos as $clave => $valor) {
            if ($valor === null || $valor === '') {
                continue;
            }

            ConfiguracionSistema::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
        }

        return back()->with('status', 'Nombres de divisiones actualizados correctamente.');
    }
}
