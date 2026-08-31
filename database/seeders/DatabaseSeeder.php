<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SucursalSeeder::class,
            NivelSeeder::class,
            PlanSeeder::class,
            CriterioEvaluacionSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
