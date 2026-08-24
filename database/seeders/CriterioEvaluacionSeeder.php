<?php

namespace Database\Seeders;

use App\Models\CriterioEvaluacion;
use App\Models\Nivel;
use Illuminate\Database\Seeder;

class CriterioEvaluacionSeeder extends Seeder
{
    public function run(): void
    {
        $criteriosBase = [
            'Flotación',
            'Respiración',
            'Técnica de brazada',
            'Coordinación',
            'Resistencia',
        ];

        Nivel::query()->ordenados()->each(function (Nivel $nivel) use ($criteriosBase) {
            foreach ($criteriosBase as $orden => $nombre) {
                CriterioEvaluacion::query()->firstOrCreate(
                    ['nivel_id' => $nivel->id, 'nombre' => $nombre],
                    ['orden' => $orden, 'activo' => true],
                );
            }
        });
    }
}
