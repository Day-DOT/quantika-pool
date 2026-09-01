<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Rol;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'sucursal_id',
        'telefono',
        'avatar_path',
        'activo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Rol::class,
            'activo' => 'boolean',
            'password_configurada' => 'boolean',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function instructor(): HasOne
    {
        return $this->hasOne(Instructor::class);
    }

    public function alumnos(): HasMany
    {
        return $this->hasMany(Alumno::class, 'tutor_user_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === Rol::SuperAdmin;
    }

    public function isAdmin(): bool
    {
        return $this->role === Rol::Admin;
    }

    public function isInstructor(): bool
    {
        return $this->role === Rol::Instructor;
    }

    public function isAlumno(): bool
    {
        return $this->role === Rol::Alumno;
    }

    /**
     * La sucursal "de este usuario" vive en distintos lugares según el rol:
     * en el propio usuario (admin), en su registro de instructor, o en la de
     * sus alumnos (tutor). Un super admin no pertenece a ninguna sucursal.
     */
    public function sucursalActual(): ?Sucursal
    {
        if ($this->sucursal_id) {
            return $this->sucursal;
        }

        if ($this->isInstructor()) {
            return $this->instructor?->sucursal;
        }

        if ($this->isAlumno()) {
            return $this->alumnos()->with('sucursal')->first()?->sucursal;
        }

        return null;
    }

    public function logoUrl(): string
    {
        $logoPath = $this->sucursalActual()?->logo_path;

        return $logoPath ? asset($logoPath) : asset('images/quantika-logo.png');
    }
}
