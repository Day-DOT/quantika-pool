<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionSistema extends Model
{
    protected $table = 'configuraciones';

    protected $fillable = ['clave', 'valor'];

    public static function valor(string $clave, string $predeterminado): string
    {
        return static::where('clave', $clave)->value('valor') ?: $predeterminado;
    }

    public static function categoriasEdad(): array
    {
        return [
            'Niños' => static::valor('categoria_ninos', 'Niños'),
            'Niñas' => static::valor('categoria_ninas', 'Niñas'),
            'Adultos mujeres' => static::valor('categoria_adultos_mujeres', 'Adultos mujeres'),
            'Adultos hombres' => static::valor('categoria_adultos_hombres', 'Adultos hombres'),
        ];
    }
}
