<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\UpdateSeguridadRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SeguridadController extends Controller
{
    public function index(): View
    {
        return view('quantika.super-admin.seguridad.index');
    }

    public function update(UpdateSeguridadRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return redirect()
            ->route('super-admin.seguridad.index')
            ->with('status', 'Contraseña actualizada correctamente.');
    }
}
