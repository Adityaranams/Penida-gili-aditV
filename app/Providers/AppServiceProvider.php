<?php

namespace App\Providers;

use App\Support\SiteSettings;
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
        // Contact details saved in the console stand in for the config defaults,
        // so every config('penida.booking.*') reader picks them up unchanged.
        SiteSettings::apply();
    }
}
