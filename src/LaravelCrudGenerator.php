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
        $this->command->newLine();
        foreach( get_object_vars( $this->crudData->entities ) as $entityName => $entityData )
        {
            $entityData = !empty( (array) $entityData )? $entityData : null;
            $files = $entityData && $entityData->files? $entityData->files : $this->configurationOptions[ 'files' ];
            $this->command->line( "<options=bold;fg=bright-yellow;>⚡</><options=bold;fg=bright-magenta;> CRUD generation for {$entityName}</>" );
            foreach( $files as $file )
            {
                if( in_array( $file, [ 'model', 'controller', 'repository' ] ) )
                $this->generateFile( $file, $entityName, $entityData );
            }
            $this->command->line( "<options=bold;fg=bright-white;>└─></> <options=bold;fg=bright-green;>✔ </><options=bold;fg=bright-cyan;> {$entityName} has been generated successfully</>" );
            $this->command->newLine();
        }
    }

    public function generateFile( string $fileType, string $entityName, object $entityData = null )
    {
        $fileType = ucfirst( $fileType );
        $class = 'TOTS\\LaravelCrudGenerator\\Generators\\' . $fileType . 'Generator';
        $generator = new $class( $entityName, $entityData );
        $generator->createFile()?
            $this->command->line( "<options=bold;fg=bright-white;>├─></> <options=bold;fg=bright-green;>✔ </><options=bold;fg=white;> {$fileType}</>" ):
            $this->command->line( "<options=bold;fg=bright-white;>├─></> <options=bold;fg=bright-red;>❌</><options=bold;fg=red;> {$fileType}</>" );
    }
}
