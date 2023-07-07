<?php

namespace TOTS\LaravelCrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use TOTS\LaravelCrudGenerator\LaravelCrudGenerator;

class LaravelCrudInstallCommand extends Command
{
    protected $signature = 'crud:install';
    protected $description = 'Install Laravel CRUD generator';

    public function handle()
    {
        $configFilePath = __DIR__ . '/../config.php';
        $laravelConfigPath = base_path( 'config/laravelCrudGenerator.php' );
        File::copy( $configFilePath, $laravelConfigPath );

        $jsonFilePath = __DIR__ . '/../laravel-crud-generator.json';
        $laravelPath = base_path( 'laravel-crud-generator.json' );
        File::copy( $jsonFilePath, $laravelPath );

        $this->info( 'Laravel CRUD generator installation has been completed successfully.' );
    }
}
