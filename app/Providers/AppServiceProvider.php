<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\Cache;

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
        $activeQna = Cache::remember('active_qna', 3600, function() {
            try {
                return \App\Models\Qna::where('active', '1')
                    ->whereNotNull('cierre')
                    ->orderBy('year', 'asc')
                    ->orderBy('qna', 'asc')
                    ->first()
                    ?? \App\Models\Qna::where('active', '1')
                    ->orderBy('year', 'asc')
                    ->orderBy('qna', 'asc')
                    ->first();
            }
            catch (\Exception $e) {
                return null;
            }
        });

        \Illuminate\Support\Facades\View::share('activeQna', $activeQna);
    }
}