<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetUserPassword extends Command
{
    protected $signature = 'users:reset-password {email} {password}';

    protected $description = 'Reset a user\'s password directly (for recovering access in production)';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error("No existe ningún usuario con el correo {$this->argument('email')}.");

            return self::FAILURE;
        }

        $user->forceFill([
            'password' => Hash::make($this->argument('password')),
        ])->save();

        $this->info("Contraseña actualizada para {$user->email} (id={$user->id}, rol={$user->role->value}).");

        return self::SUCCESS;
    }
}
