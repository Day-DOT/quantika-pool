<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Nombres de días y meses en español en toda la app ("mié 14 oct"),
        // sin cambiar el idioma general (no hay traducciones instaladas).
        Carbon::setLocale('es');
    }
}
