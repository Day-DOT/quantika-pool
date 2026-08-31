<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Rol;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\RoleRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegistroTutorController extends Controller
{
    public function create(): View
    {
        return view('auth.registro');
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'tutor_email' => ['required', 'email'],
            'alumno_nombre' => ['required', 'string', 'max:100'],
            'alumno_apellidos' => ['required', 'string', 'max:150'],
            'alumno_fecha_nacimiento' => ['required', 'date'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $mensajeGenerico = 'No encontramos una cuenta pendiente de activación con esos datos. '.
            'Verifica que el correo y los datos del alumno coincidan exactamente con los que '.
            'la escuela registró, o contacta al administrador.';

        $tutor = User::where('email', $datos['tutor_email'])
            ->where('role', Rol::Alumno->value)
            ->first();

        if (! $tutor || $tutor->password_configurada) {
            return back()->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors(['tutor_email' => $mensajeGenerico]);
        }

        $coincide = $tutor->alumnos()
            ->whereRaw('LOWER(nombre) = ?', [mb_strtolower(trim($datos['alumno_nombre']))])
            ->whereRaw('LOWER(apellidos) = ?', [mb_strtolower(trim($datos['alumno_apellidos']))])
            ->whereDate('fecha_nacimiento', $datos['alumno_fecha_nacimiento'])
            ->exists();

        if (! $coincide) {
            return back()->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors(['tutor_email' => $mensajeGenerico]);
        }

        $tutor->forceFill([
            'password' => Hash::make($datos['password']),
            'password_configurada' => true,
        ])->save();

        Auth::login($tutor);
        $request->session()->regenerate();

        return redirect()->route(RoleRedirect::homeRouteFor($tutor))
            ->with('status', '¡Tu cuenta quedó activada! Bienvenido a Quantika Pool.');
    }
}
