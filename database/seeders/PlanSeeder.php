<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $planes = [
            ['nombre' => 'Plan 2 clases/semana', 'clases_por_semana' => 2, 'precio' => 800.00],
            ['nombre' => 'Plan 3 clases/semana', 'clases_por_semana' => 3, 'precio' => 1100.00],
        ];

        foreach ($planes as $plan) {
            Plan::query()->updateOrCreate(
                ['clases_por_semana' => $plan['clases_por_semana']],
                [...$plan, 'activo' => true],
            );
        }
    }
}
