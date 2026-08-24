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
    }
}
