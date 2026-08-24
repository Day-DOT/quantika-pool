<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\SetSucursalActualRequest;
use App\Support\SucursalContext;

class SucursalContextController extends Controller
{
    /**
     * Cambia la sucursal "actual" del super admin (o la limpia para ver
     * el consolidado de todas las sucursales) y regresa a la página anterior.
     */
    public function store(SetSucursalActualRequest $request)
    {
        SucursalContext::establecer($request->validated('sucursal_id'));

        return back();
    }
}
