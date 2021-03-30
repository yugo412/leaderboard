<?php

namespace App\Providers;

use App\Services\Strava;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class SportServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('strava', function () {
            return new Strava;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
