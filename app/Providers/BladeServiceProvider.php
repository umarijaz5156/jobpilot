<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::if('candidate', function () {
            return authUser()->role == 'candidate';
        });

        Blade::if('company', function () {
            return authUser()->role == 'company';
        });

        Blade::if('currencyleft', function () {
            return config('templatecookie.currency_symbol_position') == 'left';
        });
    }
}
