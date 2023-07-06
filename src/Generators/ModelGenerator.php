<?php

namespace TOTS\LaravelCrudGenerator\Generators;

use TOTS\LaravelCrudGenerator\FileGenerator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModelGenerator extends FileGenerator
{
    // public function __construct( string $entityName, object $entityData, string $filePath = null )
    // {
    //     return parent::__construct( $entityName, $entityData, $filePath );
    // }

    public function setGeneratorType() : void
    {
        $this->generatorType = 'model';
    }

    public function setClassname() : void
    {
        $this->classname = $this->generatorData && $this->generatorData->classname? $this->generatorData->classname : $this->entityName;
    }

    public function initFileContentFromStub() : void
    {
        $this->fileContent = File::get( __DIR__ . '/../Stubs/Model.stub' );
    }

    public function generateFileContent() : void
    {
        $this->fileContent = str_replace( '{{ namespace }}', $this->classNamespace, $this->fileContent );
        $this->fileContent = str_replace( '{{ use }}', '{{ use }}', $this->fileContent );
        $this->fileContent = str_replace( '{{ classname }}', $this->classname, $this->fileContent );
        $this->fileContent = str_replace( '{{ extends }}', '{{ extends }}', $this->fileContent );
        $this->fileContent = str_replace( '{{ implements }}', '{{ implements }}', $this->fileContent );
        $this->fileContent = str_replace( '{{ use_traits }}', '{{ use_traits }}', $this->fileContent );
    }


}
