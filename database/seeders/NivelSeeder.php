<?php

namespace Database\Seeders;

use App\Models\Nivel;
use Illuminate\Database\Seeder;

class NivelSeeder extends Seeder
{
    public function run(): void
    {
        $niveles = [
            ['orden' => 1, 'nombre' => 'Estrella', 'categoria' => 'Principiante', 'color_hex' => '#ffc229', 'imagen' => 'images/Niveles/estrella.png', 'descripcion' => 'Confianza y adaptación al agua, respiración y ejercicios básicos.'],
            ['orden' => 2, 'nombre' => 'Caballito de mar', 'categoria' => 'Principiante', 'color_hex' => '#ff8c42', 'imagen' => 'images/Niveles/caballito-mar.png', 'descripcion' => 'Flotación y desplazamientos con apoyo de material.'],
            ['orden' => 3, 'nombre' => 'Medusa', 'categoria' => 'Principiante', 'color_hex' => '#ff68ad', 'imagen' => 'images/Niveles/medusa.png', 'descripcion' => 'Desplazamientos seguros y flotación sin material.'],
            ['orden' => 4, 'nombre' => 'Pulpo', 'categoria' => 'Intermedio', 'color_hex' => '#00cfff', 'imagen' => 'images/Niveles/pulpo.png', 'descripcion' => 'Movimientos de crol y primeros clavados.'],
            ['orden' => 5, 'nombre' => 'Pez', 'categoria' => 'Intermedio', 'color_hex' => '#22c8e8', 'imagen' => 'images/Niveles/pez.png', 'descripcion' => 'Dominio de crol e inicio de estilo dorso.'],
            ['orden' => 6, 'nombre' => 'Mantarraya', 'categoria' => 'Intermedio', 'color_hex' => '#37d4a0', 'imagen' => 'images/Niveles/mantarraya.png', 'descripcion' => 'Dominio del estilo dorso.'],
            ['orden' => 7, 'nombre' => 'Tortuga', 'categoria' => 'Intermedio', 'color_hex' => '#12e8ad', 'imagen' => 'images/Niveles/tortuga.png', 'descripcion' => 'Dominio del estilo de pecho.'],
            ['orden' => 8, 'nombre' => 'Foca', 'categoria' => 'Avanzado', 'color_hex' => '#55e6ff', 'imagen' => 'images/Niveles/foca.png', 'descripcion' => 'Crol, dorso y pecho; trabajo de velocidad y resistencia.'],
            ['orden' => 9, 'nombre' => 'Delfín', 'categoria' => 'Avanzado', 'color_hex' => '#15ccff', 'imagen' => 'images/Niveles/delfin.png', 'descripcion' => 'Dominio del estilo mariposa.'],
            ['orden' => 10, 'nombre' => 'Orca', 'categoria' => 'Avanzado', 'color_hex' => '#8b78ff', 'imagen' => 'images/Niveles/orca.png', 'descripcion' => 'Los cuatro estilos, salidas, vueltas y llegadas de competencia.'],
            ['orden' => 11, 'nombre' => 'Ballena', 'categoria' => 'Avanzado', 'color_hex' => '#ff6b6b', 'imagen' => 'images/Niveles/ballena.png', 'descripcion' => 'Pruebas de distancia y perfeccionamiento de estilos.'],
            ['orden' => 12, 'nombre' => 'Tiburón', 'categoria' => 'Avanzado', 'color_hex' => '#bd51ff', 'imagen' => 'images/Niveles/tiburon.png', 'descripcion' => 'Dominio técnico total de los cuatro estilos.'],
        ];

        foreach ($niveles as $nivel) {
            Nivel::query()->updateOrCreate(
                ['orden' => $nivel['orden']],
                [...$nivel, 'activo' => true],
            );
        }
    }
}
