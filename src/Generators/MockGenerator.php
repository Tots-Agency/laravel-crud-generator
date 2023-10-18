<?php

namespace TOTS\LaravelCrudGenerator\Generators;

use TOTS\LaravelCrudGenerator\FileGenerator;

class MockGenerator extends FileGenerator
{
    protected string $attributes = '';
    protected int $count;

    public function setFileContent() : void
    {
        $this->addModelUrl();
        $this->setClassname();
        $this->setCount();
    }

    public function addModelUrl() : void
    {
        parent::addFileUseUrl( $this->entityData->modelUrl );
    }

    public function setClassname() : void
    {
        $classname = $this->fileData && property_exists( $this->fileData, 'classname' )? $this->fileData->classname : $this->entityName . ucfirst( $this->fileType ) . 'Seeder';
        $this->classname = $classname ?? '';
    }

    public function setCount()
    {
        $this->count = $this->fileData && property_exists( $this->fileData, 'count' )? $this->fileData->count : $this->configurationOptions[ $this->fileType ][ 'count' ];
    }

    public function generateFileContent() : void
    {
        parent::generateFileContent();
        $this->fileContent = str_replace( '{{ model }}', $this->entityData->modelClassname, $this->fileContent );
        $this->fileContent = str_replace( '{{ count }}', $this->count, $this->fileContent );
    }
}
