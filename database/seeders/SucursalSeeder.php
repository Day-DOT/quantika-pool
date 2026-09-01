<?php

namespace Database\Seeders;

use App\Models\Sucursal;
use Illuminate\Database\Seeder;

class SucursalSeeder extends Seeder
{
    public function run(): void
    {
        Sucursal::query()->firstOrCreate(
            ['codigo' => 'SUC1'],
            ['nombre' => 'Sucursal 1', 'activa' => true],
        );

        Sucursal::query()->firstOrCreate(
            ['codigo' => 'SUC2'],
            ['nombre' => 'Sucursal 2', 'activa' => true],
        );

        // No se usa updateOrCreate para el logo: el nombre de la sucursal
        // pudo haber sido personalizado (p.ej. "Aqualix Wellness Club") y no
        // debe revertirse en cada redeploy. Solo se rellena el logo si está
        // vacío, sin tocar nombre ni otros datos ya capturados.
        Sucursal::query()
            ->where('codigo', 'SUC2')
            ->whereNull('logo_path')
            ->update(['logo_path' => 'images/logo-sucursal-2.png']);
    }
}
