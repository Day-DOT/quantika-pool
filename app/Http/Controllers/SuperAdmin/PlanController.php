<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StorePlanRequest;
use App\Http\Requests\SuperAdmin\UpdatePlanRequest;
use App\Models\Plan;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlanController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', Plan::class);

        $planes = Plan::withCount('alumnos')->orderBy('clases_por_semana')->get();

        return view('quantika.super-admin.planes.index', [
            'planes' => $planes,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Plan::class);

        return view('quantika.super-admin.planes.create');
    }

    public function store(StorePlanRequest $request): RedirectResponse
    {
        $this->authorize('create', Plan::class);

        Plan::create($request->validated());

        return redirect()
            ->route('super-admin.planes.index')
            ->with('status', 'Plan creado correctamente.');
    }

    public function edit(Plan $plan): View
    {
        $this->authorize('update', $plan);

        return view('quantika.super-admin.planes.edit', [
            'plan' => $plan,
        ]);
    }

    public function update(UpdatePlanRequest $request, Plan $plan): RedirectResponse
    {
        $this->authorize('update', $plan);

        $plan->update($request->validated());

        return redirect()
            ->route('super-admin.planes.index')
            ->with('status', 'Plan actualizado correctamente.');
    }
}
