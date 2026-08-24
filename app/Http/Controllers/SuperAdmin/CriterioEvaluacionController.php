<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreCriterioEvaluacionRequest;
use App\Http\Requests\SuperAdmin\UpdateCriterioEvaluacionRequest;
use App\Models\CriterioEvaluacion;
use App\Models\Nivel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CriterioEvaluacionController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', CriterioEvaluacion::class);

        $criterios = CriterioEvaluacion::query()
            ->with('nivel')
            ->when($request->filled('nivel_id'), fn ($q) => $q->where('nivel_id', $request->integer('nivel_id')))
            ->orderBy('nivel_id')
            ->orderBy('orden')
            ->get();

        return view('quantika.super-admin.criterios.index', [
            'criterios' => $criterios,
            'niveles' => Nivel::ordenados()->get(),
            'nivelId' => $request->integer('nivel_id') ?: null,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', CriterioEvaluacion::class);

        return view('quantika.super-admin.criterios.create', [
            'niveles' => Nivel::ordenados()->get(),
        ]);
    }

    public function store(StoreCriterioEvaluacionRequest $request): RedirectResponse
    {
        $this->authorize('create', CriterioEvaluacion::class);

        CriterioEvaluacion::create($request->validated());

        return redirect()
            ->route('super-admin.criterios.index')
            ->with('status', 'Criterio de evaluación creado correctamente.');
    }

    public function edit(CriterioEvaluacion $criterio): View
    {
        $this->authorize('update', $criterio);

        return view('quantika.super-admin.criterios.edit', [
            'criterio' => $criterio,
            'niveles' => Nivel::ordenados()->get(),
        ]);
    }

    public function update(UpdateCriterioEvaluacionRequest $request, CriterioEvaluacion $criterio): RedirectResponse
    {
        $this->authorize('update', $criterio);

        $criterio->update($request->validated());

        return redirect()
            ->route('super-admin.criterios.index')
            ->with('status', 'Criterio de evaluación actualizado correctamente.');
    }

    public function destroy(CriterioEvaluacion $criterio): RedirectResponse
    {
        $this->authorize('delete', $criterio);

        $criterio->delete();

        return redirect()
            ->route('super-admin.criterios.index')
            ->with('status', 'Criterio de evaluación eliminado correctamente.');
    }
}
