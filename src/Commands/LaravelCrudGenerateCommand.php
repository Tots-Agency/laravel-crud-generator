<?php

namespace TOTS\LaravelCrudGenerator\Commands;

use Illuminate\Console\Command;
use TOTS\LaravelCrudGenerator\LaravelCrudGenerator;

class LaravelCrudGenerateCommand extends Command
{
    protected $signature = 'crud:generate {entity} {--options= : The options for CRUD generation}';

    protected $description = 'Generate CRUD files for a given entity';

    public function handle()
    {
        $entityName = $this->argument( 'entity' );
        $crudOptions = explode( ',', $this->option( 'options' ) );

        $crudGenerator = new LaravelCrudGenerator( $entityName, $crudOptions );
        $crudGenerator->generateFiles();

        $this->info( 'CRUD for ' . $entityName . ' successfully generated.' );
    }
}
