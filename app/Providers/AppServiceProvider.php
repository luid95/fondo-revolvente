<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Fondo;

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
        View::composer('*', function ($view) {
            $fondo = Fondo::whereNull('deleted_at')->first();
            $monto = $fondo?->monto ?? 0;
            $view->with('fondo_monto', $monto);
        });
    }
}
