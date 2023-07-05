<?php

namespace TOTS\LaravelCrudGenerator\Generators;

use TOTS\LaravelCrudGenerator\FileGenerator;

class ModelGenerator extends FileGenerator
{
    protected string $entityName;
    protected array $entityData;
    protected array $configurationOptions;
    protected string $filePath;

    public function __construct( string $entityName, array $options, string $filePath = null )
    {
        parent::__construct( $entityName, $options, $filePath );
        if( !$this->filePath ) $this->filePath = $this->configurationOptions['model'][ 'file-path' ];
    }

    public function generateFiles()
    {
        foreach( $this->options as $file )
        {
            $method = 'create' . ucfirst( $file );
            $this->$method();
        }
    }
}
