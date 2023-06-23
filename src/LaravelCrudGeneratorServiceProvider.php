<?php

namespace TOTS\LaravelCrudGenerator;

use Illuminate\Support\ServiceProvider;

class LaravelCrudGeneratorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            Commands\LaravelCrudGenerateCommand::class,
        ]);
    }
}
