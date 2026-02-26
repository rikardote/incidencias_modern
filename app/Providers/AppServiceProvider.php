<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;

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
        Vite::useBuildDirectory('dist');

        // Compartir la QNA activa con absolutamente todas las vistas
        try {
            // 1. Buscamos la QNA activa más antigua que TENGA fecha de cierre (la que urge cerrar)
            // 2. Si ninguna tiene fecha, buscamos la QNA activa más antigua (la subsecuente a la última cerrada)
            $activeQna = \App\Models\Qna::where('active', '1')
                ->whereNotNull('cierre')
                ->orderBy('year', 'asc')
                ->orderBy('qna', 'asc')
                ->first() 
                ?? \App\Models\Qna::where('active', '1')
                ->orderBy('year', 'asc')
                ->orderBy('qna', 'asc')
                ->first();

            \Illuminate\Support\Facades\View::share('activeQna', $activeQna);
        } catch (\Exception $e) {
            // Silencio en caso de que la tabla no exista aún (migraciones)
        }
    }
}
