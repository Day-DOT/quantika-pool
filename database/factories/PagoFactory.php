<?php

namespace Database\Factories;

use App\Enums\ConceptoPago;
use App\Enums\EstadoPago;
use App\Enums\MetodoPago;
use App\Models\Alumno;
use App\Models\Pago;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pago>
 */
class PagoFactory extends Factory
{
    protected $model = Pago::class;

    public function definition(): array
    {
        $estado = fake()->randomElement([
            EstadoPago::Pagado->value,
            EstadoPago::Pendiente->value,
            EstadoPago::EnRevision->value,
        ]);

        return [
            'alumno_id' => Alumno::factory(),
            'sucursal_id' => Sucursal::factory(),
            'concepto' => ConceptoPago::Mensualidad->value,
            'periodo' => now()->format('Y-m'),
            'monto' => fake()->randomElement([450, 500, 550, 600]),
            'fecha_vencimiento' => now()->addDays(10)->toDateString(),
            'fecha_pago' => $estado === EstadoPago::Pagado->value ? fake()->dateTimeBetween('-15 days', 'now') : null,
            'metodo_pago' => $estado === EstadoPago::Pagado->value ? fake()->randomElement(MetodoPago::cases())->value : null,
            'estado' => $estado,
            'observaciones' => null,
        ];
    }
}
