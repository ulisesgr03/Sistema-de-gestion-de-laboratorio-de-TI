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
        View::composer(['layouts.*', 'public.*', 'private.*', 'partials.estructura-bm-3d', 'partials.bm-interactivo'], function ($view) {
            $view->with('institutionName', config('app.institution_name', 'MetalNet'));
            $view->with('institutionFull', config('app.institution_full', 'MetalNet · Laboratorios de Ingeniería en Sistemas'));
            $view->with('bmLabs', CatalogoLaboratorios::laboratorios());
            $view->with('bmTotales', CatalogoLaboratorios::totales());
            $view->with('bmLabsJs', CatalogoLaboratorios::paraJs());
            $view->with('bmSoftwareJs', CatalogoLaboratorios::software());
            $view->with('bmInstrumentalJs', CatalogoLaboratorios::instrumental());
        });
    }
}
