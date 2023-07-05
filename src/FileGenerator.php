<?php

namespace TOTS\LaravelCrudGenerator;

abstract class FileGenerator
{
    protected string $entityName;
    protected array $entityData;
    protected string $filePath;
    protected array $configurationOptions;

    public function __construct( string $entityName, array $entityData, string $filePath = null )
    {
        $this->entityName = $entityName;
        $this->entityData = $entityData;
        $this->filePath = $filePath;
        $this->configurationOptions = require config_path( 'laravelCrudGenerator.php' );
    }

    public function generateFile()
    {
        foreach( $this->options as $file )
        {
            $method = 'create' . ucfirst( $file );
            $this->$method();
        }
    }
}
