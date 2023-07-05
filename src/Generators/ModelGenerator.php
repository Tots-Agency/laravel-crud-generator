<?php

namespace TOTS\LaravelCrudGenerator\Generators;

use TOTS\LaravelCrudGenerator\FileGenerator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModelGenerator extends FileGenerator
{
    public function setGeneratorType() : void
    {
        $this->generatorType = 'model';
    }

    public function setClassname()
    {
        $classname = $this->generatorData && $this->generatorData->classname? $this->generatorData->classname : $this->entitySingularName;
        $this->classname = Str::camel( $classname );
    }

    public function initFileContentFromStub() : void
    {
        $this->fileContent = File::get( __DIR__ . '/Stubs/Model.stub' );
    }

    public function generateFileContent() : void
    {
        $this->fileContent = str_replace( '{{entity}}', $this->entityName, $this->fileContent );
    }


}
