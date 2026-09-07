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
            'Bebés' => static::valor('categoria_bebes', 'Bebés'),
            'Niños' => static::valor('categoria_ninos', 'Niños'),
            'Adultos' => static::valor('categoria_adultos', 'Adultos'),
        ];
    }
}
