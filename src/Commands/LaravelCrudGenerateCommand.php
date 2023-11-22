<?php

namespace TOTS\LaravelCrudGenerator\Commands;

use Illuminate\Console\Command;
use TOTS\LaravelCrudGenerator\LaravelCrudGenerator;

class LaravelCrudGenerateCommand extends Command
{
    protected $signature = 'crud:generate {--path= : The path for CRUD generation file?}';
    protected $description = 'Generate CRUD files';

    public function handle()
    {
        $crudGeneratorFilePath = $this->option( 'path' );

        $crudGenerator = new LaravelCrudGenerator( $this, $crudGeneratorFilePath );
        $crudGenerator->generateFiles();
    }
}
