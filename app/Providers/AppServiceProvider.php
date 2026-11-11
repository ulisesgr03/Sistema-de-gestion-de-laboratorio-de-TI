<?php

namespace App\Providers;

use App\Support\CatalogoLaboratorios;
use Illuminate\Support\Facades\View;
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
        View::composer('*', function ($view) {
            $labs = CatalogoLaboratorios::laboratorios();
            $totales = CatalogoLaboratorios::totales();
            $labsJs = CatalogoLaboratorios::paraJs();
            $software = CatalogoLaboratorios::software();
            $instrumental = CatalogoLaboratorios::instrumental();
            $solicitudes = CatalogoLaboratorios::solicitudesPendientes();
            $tareas = CatalogoLaboratorios::tareasAdmin();

            $view->with('institutionName', config('app.institution_name', 'MetalNet'));
            $view->with('institutionFull', config('app.institution_full', 'MetalNet · Laboratorios de Ingeniería en Sistemas'));
            $view->with('bmLabs', $labs);
            $view->with('bmTotales', $totales);
            $view->with('bmLabsJs', $labsJs);
            $view->with('bmSoftwareJs', $software);
            $view->with('bmInstrumentalJs', $instrumental);
            $view->with('bmSolicitudes', $solicitudes);
            $view->with('bmTareas', $tareas);
        });
    }
}
