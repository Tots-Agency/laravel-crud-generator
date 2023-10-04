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
            // $entityData = !empty( (array) $entityData )? $entityData : null;
            // $files = $entityData && $entityData->files? $entityData->files : $this->configurationOptions[ 'files' ];
            $entityData = $this->setEntityData( $entityName, $entityData );
            $this->command->line( "<options=bold;fg=bright-yellow;>⚡</><options=bold;fg=bright-magenta;> CRUD generation for {$entityName}</>" );
            foreach( $entityData->files as $file )
            {
                if( in_array( $file, [ 'routes', 'model', 'controller', 'service', 'migration', 'resource' ] ) )
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

    public function setEntityData( string $entityName, object $entityData )
    {
        $entityData = !empty( (array) $entityData )? $entityData : null;
        if( !$entityData ) $entityData = new \stdClass();
        $entityData->files = $entityData && property_exists( $entityData, 'files' )? $entityData->files : $this->configurationOptions[ 'files' ];

        foreach( [ 'model', 'controller', 'service' ] as $globalEntity )
            $entityData = $this->setGlobalEntity( $globalEntity, $entityName, $entityData );
        return $entityData;
    }

    public function setGlobalEntity( string $globalEntity, string $entityName, object $entityData )
    {
        $entityIsSet = property_exists( $entityData, $globalEntity );
        $classnameAttribute = $globalEntity."Classname";
        $filePathAttribute = $globalEntity."FilePath";
        $namespaceAttribute = $globalEntity."Namespace";
        $urlAttribute = $globalEntity."Url";
        $defaultClassname = $globalEntity == 'model'? '' : ucfirst( $globalEntity );
        $entityData->$classnameAttribute = $entityIsSet && property_exists( $entityData->$globalEntity, 'classname' )? $entityData->$globalEntity->classname : $entityName . $defaultClassname;
        $entityData->$filePathAttribute = $entityIsSet && property_exists( $entityData->$globalEntity, 'filePath' )? $entityData->$globalEntity->filePath : $this->configurationOptions[ $globalEntity ][ 'file_path' ];
        $entityData->$namespaceAttribute = $entityIsSet && property_exists( $entityData->$globalEntity, 'namespace' )? $entityData->$globalEntity->namespace : $this->configurationOptions[ $globalEntity ][ 'namespace' ];
        $entityData->$urlAttribute = $entityData->$namespaceAttribute . '\\' . $entityData->$classnameAttribute;
        return $entityData;
    }
}
