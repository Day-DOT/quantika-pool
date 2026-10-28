<?php

namespace Tests\Feature;

use App\Enums\EstadoClaseMuestra;
use App\Models\Alumno;
use App\Models\Carril;
use App\Models\ClaseMuestra;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Instructor;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Clases muestra: gratuitas, para quien todavía no es alumno, y una sola
 * vez por persona.
 */
class ClasesMuestraTest extends TestCase
{
    use RefreshDatabase;

    private Sucursal $sucursal;

    private User $admin;

    private Horario $lunes;

    protected function setUp(): void
    {
        parent::setUp();

        // Lunes 12 de octubre de 2026, 9:00.
        $this->travelTo(Carbon::create(2026, 10, 12, 9));

        $this->sucursal = Sucursal::factory()->create();
        $this->admin = User::factory()->admin($this->sucursal->id)->create();
        $this->lunes = Horario::factory()->create([
            'sucursal_id' => $this->sucursal->id,
            'instructor_id' => Instructor::factory()->create(['sucursal_id' => $this->sucursal->id])->id,
            'carril_id' => Carril::factory()->create(['sucursal_id' => $this->sucursal->id])->id,
            'nivel_id' => null,
            'dia_semana' => 1,
            'hora_inicio' => '16:00:00',
            'hora_fin' => '17:00:00',
            'capacidad_maxima' => 6,
            'nombre_grupo' => 'Grupo Lunes',
            'activo' => true,
        ]);
    }

    /**
     * @param  array<string, mixed>  $cambios
     * @return array<string, mixed>
     */
    private function datos(array $cambios = []): array
    {
        return [
            '_form' => 'clase_muestra',
            'nombre_alumno' => 'Sofía Ramírez',
            'edad' => 6,
            'nombre_tutor' => 'Ana López',
            'telefono' => '33 1234 5678',
            'email' => 'ana@example.com',
            'horario_id' => $this->lunes->id,
            'fecha' => '2026-10-12',
            ...$cambios,
        ];
    }

    public function test_se_agenda_una_clase_muestra_y_el_instructor_ve_nombre_edad_y_que_es_muestra(): void
    {
        $this->actingAs($this->admin)
            ->from(route('horarios.index'))
            ->post(route('clases-muestra.store'), $this->datos())
            ->assertRedirect(route('horarios.index'))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        $clase = ClaseMuestra::sole();
        $this->assertSame(EstadoClaseMuestra::Programada, $clase->estado);
        $this->assertSame('3312345678', $clase->telefono_normalizado);

        $instructor = $this->lunes->instructor->user;

        $this->actingAs($instructor)
            ->get(route('instructor.dashboard'))
            ->assertOk()
            ->assertSee('★ Sofía Ramírez (6 años) · Clase muestra', false);

        $this->actingAs($instructor)
            ->get(route('instructor.agenda'))
            ->assertOk()
            ->assertSee('Sofía Ramírez (6 años) · Clase muestra', false);

        $this->actingAs($instructor)
            ->get(route('instructor.grupos.show', $this->lunes))
            ->assertOk()
            ->assertSee('Clases muestra de hoy', false)
            ->assertSee('Sofía Ramírez')
            ->assertSee('Clase muestra · solo hoy', false);

        // El tablero y el dashboard del admin también la muestran.
        $tablero = $this->actingAs($this->admin)->get(route('horarios.index'))->assertOk();
        $this->assertSame('Sofía Ramírez (6 años)', $tablero->viewData('detallesClases')[$this->lunes->id]['muestras_semana'][0]['nombre']);

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('★ Sofía Ramírez (6 años) · Clase muestra', false);
    }

    public function test_avisa_si_ya_tomo_una_clase_muestra_aunque_el_telefono_este_escrito_distinto(): void
    {
        ClaseMuestra::create([
            ...$this->datos(['fecha' => '2026-03-09']),
            'sucursal_id' => $this->sucursal->id,
            'hora_inicio' => '16:00:00',
            'hora_fin' => '17:00:00',
            'estado' => EstadoClaseMuestra::Asistio->value,
        ]);

        $this->actingAs($this->admin)
            ->post(route('clases-muestra.store'), $this->datos([
                'nombre_alumno' => 'Sofi R.',
                'telefono' => '+52 (33) 1234-5678',
                'email' => null,
            ]))
            ->assertSessionHasErrors(['clase_muestra_repetida' => 'Esta persona ya tomó anteriormente una clase muestra: Sofía Ramírez (6 años), tutor Ana López, tel. 33 1234 5678, el 09/03/2026 en Grupo Lunes (Asistió). La clase muestra es gratuita una sola vez; si aun así quieres agendarla, marca «Agendar de todos modos».']);

        $this->assertSame(1, ClaseMuestra::count());

        // Con la confirmación sí se agenda.
        $this->actingAs($this->admin)
            ->post(route('clases-muestra.store'), $this->datos(['nombre_alumno' => 'Sofi R.', 'confirmar_repetida' => 1]))
            ->assertSessionHasNoErrors();

        $this->assertSame(2, ClaseMuestra::count());
    }

    public function test_reconoce_a_la_persona_por_correo_o_por_nombre_sin_acentos(): void
    {
        ClaseMuestra::create([
            ...$this->datos(['fecha' => '2026-03-09']),
            'sucursal_id' => $this->sucursal->id,
            'hora_inicio' => '16:00:00',
            'hora_fin' => '17:00:00',
            'estado' => EstadoClaseMuestra::NoAsistio->value,
        ]);

        $this->actingAs($this->admin)
            ->post(route('clases-muestra.store'), $this->datos(['nombre_alumno' => 'Otro Niño', 'telefono' => '5550001111', 'email' => 'ANA@example.com']))
            ->assertSessionHasErrors('clase_muestra_repetida');

        $this->actingAs($this->admin)
            ->post(route('clases-muestra.store'), $this->datos(['nombre_alumno' => '  sofia   ramirez ', 'telefono' => '5550001111', 'email' => null]))
            ->assertSessionHasErrors('clase_muestra_repetida');
    }

    public function test_una_clase_muestra_cancelada_no_cuenta_como_tomada(): void
    {
        ClaseMuestra::create([
            ...$this->datos(['fecha' => '2026-03-09']),
            'sucursal_id' => $this->sucursal->id,
            'hora_inicio' => '16:00:00',
            'hora_fin' => '17:00:00',
            'estado' => EstadoClaseMuestra::Cancelada->value,
        ]);

        $this->actingAs($this->admin)
            ->post(route('clases-muestra.store'), $this->datos())
            ->assertSessionHasNoErrors();
    }

    public function test_la_clase_muestra_ocupa_lugar_en_el_grupo(): void
    {
        $this->lunes->update(['capacidad_maxima' => 2]);
        Inscripcion::factory()->create([
            'alumno_id' => Alumno::factory()->create(['sucursal_id' => $this->sucursal->id])->id,
            'horario_id' => $this->lunes->id,
            'fecha_inicio' => today()->subMonth(),
            'activa' => true,
        ]);

        $this->actingAs($this->admin)->post(route('clases-muestra.store'), $this->datos())->assertSessionHasNoErrors();

        $this->actingAs($this->admin)
            ->post(route('clases-muestra.store'), $this->datos(['nombre_alumno' => 'Mateo Cruz', 'telefono' => '5550001111', 'email' => null]))
            ->assertSessionHasErrors(['horario_id' => 'Ese grupo ya no tiene cupo disponible para esa fecha.']);
    }

    public function test_no_se_agenda_en_un_dia_distinto_al_del_grupo_ni_en_el_pasado(): void
    {
        $this->actingAs($this->admin)
            ->post(route('clases-muestra.store'), $this->datos(['fecha' => '2026-10-13']))
            ->assertSessionHasErrors('fecha');

        $this->actingAs($this->admin)
            ->post(route('clases-muestra.store'), $this->datos(['fecha' => '2026-10-05']))
            ->assertSessionHasErrors('fecha');

        $this->assertSame(0, ClaseMuestra::count());
    }

    public function test_el_instructor_marca_la_asistencia_de_la_clase_muestra_de_hoy(): void
    {
        $this->actingAs($this->admin)->post(route('clases-muestra.store'), $this->datos());
        $clase = ClaseMuestra::sole();

        $this->actingAs($this->lunes->instructor->user)
            ->post(route('instructor.grupos.muestra.asistencia', [$this->lunes, $clase]), ['asistio' => 1])
            ->assertSessionHas('status');

        $this->assertSame(EstadoClaseMuestra::Asistio, $clase->fresh()->estado);
    }

    public function test_el_historial_lista_y_busca_las_clases_muestra(): void
    {
        $this->actingAs($this->admin)->post(route('clases-muestra.store'), $this->datos());
        $this->actingAs($this->admin)->post(route('clases-muestra.store'), $this->datos([
            'nombre_alumno' => 'Mateo Cruz', 'nombre_tutor' => 'Luis Cruz', 'telefono' => '5550001111', 'email' => null,
        ]));

        $this->actingAs($this->admin)
            ->get(route('clases-muestra.index'))
            ->assertOk()
            ->assertSee('Sofía Ramírez')
            ->assertSee('Mateo Cruz');

        $this->actingAs($this->admin)
            ->get(route('clases-muestra.index', ['q' => '555 000']))
            ->assertOk()
            ->assertSee('Mateo Cruz')
            ->assertDontSee('Sofía Ramírez');
    }
}
