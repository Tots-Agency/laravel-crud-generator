<?php

namespace TOTS\LaravelCrudGenerator;

use Illuminate\Support\ServiceProvider;

class LaravelCrudGeneratorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->booted( function ()
        {
            $this->publishes( [ __DIR__.'/config.php' => config_path( 'laravelCrudGenerator.php' ), ], 'config' );
        } );

        $this->commands( [
            Commands\LaravelCrudGenerateCommand::class,
            Commands\LaravelCrudInstallCommand::class,
        ] );
    }
}
