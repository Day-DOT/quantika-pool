<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ContratoController extends Controller
{
    use AuthorizesRequests;

    public function create(Alumno $alumno): View
    {
        $this->authorize('update', $alumno);

        $alumno->load('nivel', 'plan', 'sucursal');

        $inscripcion = $alumno->inscripciones()
            ->where('activa', true)
            ->with('horario')
            ->first();

        return view('quantika.alumnos.contrato', [
            'alumno' => $alumno,
            'esMenorDeEdad' => $alumno->fecha_nacimiento?->age < 18,
            'horario' => $inscripcion?->horario,
        ]);
    }

    public function store(Request $request, Alumno $alumno): RedirectResponse
    {
        $this->authorize('update', $alumno);

        $esMenorDeEdad = $alumno->fecha_nacimiento?->age < 18;

        $datos = $request->validate([
            'cuota_inscripcion' => ['nullable', 'numeric', 'min:0'],
            'lugar' => ['nullable', 'string', 'max:150'],
            'firma_titular_nombre' => ['required', 'string', 'max:150'],
            'firma_titular_imagen' => ['required', 'string'],
            'firma_responsable_nombre' => ['required', 'string', 'max:150'],
            'firma_responsable_imagen' => ['required', 'string'],
        ]);

        $inscripcion = $alumno->inscripciones()
            ->where('activa', true)
            ->with('horario')
            ->first();

        $html = view('quantika.alumnos.contrato-pdf', [
            'alumno' => $alumno->load('nivel', 'plan', 'sucursal'),
            'horario' => $inscripcion?->horario,
            'esMenorDeEdad' => $esMenorDeEdad,
            'cuotaInscripcion' => $datos['cuota_inscripcion'] ?? null,
            'lugar' => $datos['lugar'] ?? null,
            'firmaTitularNombre' => $datos['firma_titular_nombre'],
            'firmaTitularImagen' => $datos['firma_titular_imagen'],
            'firmaResponsableNombre' => $datos['firma_responsable_nombre'],
            'firmaResponsableImagen' => $datos['firma_responsable_imagen'],
            'fechaFirma' => now(),
        ])->render();

        $pdf = Pdf::loadHTML($html)->setPaper('letter');

        if ($alumno->contrato_firmado_path) {
            Storage::disk('public')->delete($alumno->contrato_firmado_path);
        }

        $ruta = 'alumnos/documentos/contrato-'.$alumno->id.'-'.now()->timestamp.'.pdf';
        Storage::disk('public')->put($ruta, $pdf->output());

        $alumno->update(['contrato_firmado_path' => $ruta]);

        return redirect()->route('alumnos.show', $alumno)
            ->with('status', 'Contrato firmado y guardado correctamente.');
    }
}
