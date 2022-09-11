<?php

namespace App\Services\SendinBlue;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class SendinBlueServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('sendinBlue', function() {
            return App::make('App\Services\SendinBlue\SendinBlueService');
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
