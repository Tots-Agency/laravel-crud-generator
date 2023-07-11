<?php

namespace TOTS\LaravelCrudGenerator;

use Illuminate\Console\Command;

class LaravelCrudGenerator
{
    private object $crudData;
    private array $configurationOptions;
    private Command $command;

    public function __construct( $command, $filePath = null )
    {
        $this->command = $command;
        $this->configurationOptions = require config_path( 'laravelCrudGenerator.php' );
        $this->crudData = json_decode( file_get_contents( $filePath?  $filePath : $this->configurationOptions[ 'default_file_path' ] ) );
    }

    public function generateFiles()
    {
        foreach( get_object_vars( $this->crudData->entities ) as $entityName => $entityData )
        {
            $entityData = !empty( (array) $entityData )? $entityData : null;
            $files = $entityData && $entityData->files? $entityData->files : $this->configurationOptions[ 'files' ];
            foreach( $files as $file )
            {
                if( in_array( $file, [ 'model', 'controller', 'service' ] ) )
                $this->generateFile( $file, $entityName, $entityData );
            }
        }
    }

    public function generateFile( string $fileType, string $entityName, object $entityData = null )
    {
        $fileType = ucfirst( $fileType );
        $class = 'TOTS\\LaravelCrudGenerator\\Generators\\' . $fileType . 'Generator';
        $generator = new $class( $entityName, $entityData );
        $generator->createFile()?
            $this->command->info( "✔ {$fileType} {$entityName} has been created successfully." ):
            $this->command->warn( "❌ {$fileType} {$entityName} hasn't been created since already exist." );
    }
}
