<?php

namespace App\Providers;

use App\Contracts\Workout;
use App\Http\Controllers\Channels\ReliveController;
use App\Http\Controllers\Channels\StravaController;
use App\Services\Relive;
use App\Services\Strava;
use Illuminate\Support\ServiceProvider;

class WorkoutServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // default bind to Strava service
        $this->app->bind(Workout::class, Strava::class);

        $this->app->when(StravaController::class)
            ->needs(Workout::class)
            ->give(function () {
                return new Strava;
            });

        $this->app->when(ReliveController::class)
            ->needs(Workout::class)
            ->give(function () {
                return new Relive;
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
