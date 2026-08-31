<?php

namespace App\Models;

use App\Enums\ConceptoPago;
use App\Enums\EstadoAlumno;
use App\Enums\EstadoCita;
use App\Enums\EstadoInscripcion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Alumno extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutor_user_id',
        'sucursal_id',
        'nivel_id',
        'plan_id',
        'nombre',
        'apellidos',
        'fecha_nacimiento',
        'telefono',
        'email',
        'observaciones',
        'estado',
        'fecha_inscripcion',
        'qr_token',
        'certificado_medico_path',
        'identificacion_path',
        'foto_path',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'fecha_inscripcion' => 'date',
            'estado' => EstadoAlumno::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Alumno $alumno) {
            $alumno->qr_token ??= Str::random(40);
        });
    }

    /**
     * URL absoluta que codifica el código QR de asistencia de este alumno.
     * Al abrirse (escaneada por el staff), registra su asistencia del día.
     */
    public function qrUrl(): string
    {
        return route('asistencia.registrar', $this->qr_token);
    }

    public function nombreCompleto(): string
    {
        return trim("{$this->nombre} {$this->apellidos}");
    }

    public function tutorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_user_id');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function historialNiveles(): HasMany
    {
        return $this->hasMany(AlumnoNivelHistorial::class);
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    /**
     * El último pago de mensualidad registrado (por fecha de vencimiento),
     * sin importar su estado. Sirve de referencia para estimar cuándo le
     * toca el siguiente.
     */
    public function ultimoPagoMensualidad(): HasOne
    {
        return $this->hasOne(Pago::class)
            ->where('concepto', ConceptoPago::Mensualidad->value)
            ->ofMany('fecha_vencimiento', 'max');
    }

    public function scopeDeSucursal($query, int $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }

    public function scopeDelTutor($query, int $tutorUserId)
    {
        return $query->where('tutor_user_id', $tutorUserId);
    }

    /**
     * Cuántas clases (Citas no canceladas) tiene esta semana (lunes a domingo).
     */
    public function clasesEstaSemana(): int
    {
        $inicioSemana = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $finSemana = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        return $this->citas()
            ->whereBetween('fecha', [$inicioSemana->toDateString(), $finSemana->toDateString()])
            ->where('estado', '!=', EstadoCita::Cancelada->value)
            ->count();
    }

    /**
     * Límite de clases semanales según el plan del alumno. Sin plan
     * asignado no se aplica límite.
     */
    public function limiteSemanal(): ?int
    {
        return $this->plan?->clases_por_semana;
    }

    public function tieneClasesDisponiblesEstaSemana(): bool
    {
        $limite = $this->limiteSemanal();

        return $limite === null || $this->clasesEstaSemana() < $limite;
    }

    /**
     * Inscripciones que ya ocupan un lugar dentro del plan del alumno:
     * las activas (aprobadas) y las que aún están pendientes de aprobación.
     */
    public function inscripcionesVigentes()
    {
        return $this->inscripciones()->where(function ($query) {
            $query->where('activa', true)
                ->orWhere('estado', EstadoInscripcion::Pendiente->value);
        });
    }

    /**
     * Cuántos horarios más puede reservar el alumno según su plan. Un
     * alumno sin plan asignado no puede reservar ninguno.
     */
    public function cuposDisponiblesParaReservar(): int
    {
        if (! $this->plan_id) {
            return 0;
        }

        $usados = $this->inscripcionesVigentes()->count();

        return max(0, $this->plan->clases_por_semana - $usados);
    }

    /**
     * Fecha estimada del siguiente pago de mensualidad: un mes después del
     * vencimiento de su último pago registrado, o un mes después de su
     * fecha de inscripción si todavía no tiene ningún pago. Es una
     * estimación (no depende de que el Admin ya haya registrado el
     * siguiente cobro pendiente).
     */
    public function proximaFechaPago(): ?Carbon
    {
        $referencia = $this->ultimoPagoMensualidad?->fecha_vencimiento ?? $this->fecha_inscripcion;

        return $referencia?->copy()->addMonthNoOverflow();
    }
}
