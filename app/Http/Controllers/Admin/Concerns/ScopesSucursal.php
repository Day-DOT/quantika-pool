<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Sucursal;
use App\Support\SucursalContext;
use Illuminate\Database\Eloquent\Builder;

trait ScopesSucursal
{
    /**
     * Id de la sucursal actual, o null si el super admin está en vista global.
     */
    protected function sucursalId(): ?int
    {
        return SucursalContext::actualId();
    }

    /**
     * Sucursales visibles para el usuario actual (todas si es vista global).
     */
    protected function sucursalesVisibles()
    {
        $id = $this->sucursalId();

        return $id
            ? Sucursal::query()->where('id', $id)->orderBy('id')->get()
            : Sucursal::query()->orderBy('id')->get();
    }

    /**
     * Aplica el filtro de sucursal actual a una consulta, si aplica.
     */
    protected function aplicarSucursal(Builder $query, string $columna = 'sucursal_id'): Builder
    {
        $id = $this->sucursalId();

        if ($id) {
            $query->where($columna, $id);
        }

        return $query;
    }
}
