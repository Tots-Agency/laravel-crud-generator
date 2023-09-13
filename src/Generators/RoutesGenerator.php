<?php

namespace TOTS\LaravelCrudGenerator\Generators;

use Illuminate\Support\Facades\File;
use TOTS\LaravelCrudGenerator\FileGenerator;

class RoutesGenerator extends FileGenerator
{
    protected string $controller = '';
    protected string $controllerUrl = '';
    protected string $entity = '';
    protected string $entityPlural = '';

    public function setFileContent() : void
    {

    }

    public function generateFileContent() : void
    {
        $this->fileContent = File::get( __DIR__ . '/../Stubs/routes.stub' );
        $this->fileContent = str_replace( '{{ controller_url }}', $this->entityData->controllerUrl, $this->fileContent );
        $this->fileContent = str_replace( '{{ controller }}', $this->entityData->controllerClassname, $this->fileContent );
        $this->fileContent = str_replace( '{{ entity }}', $this->entitySingularName, $this->fileContent );
        $this->fileContent = str_replace( '{{ entity_plural }}', $this->entityPluralName, $this->fileContent );
    }
}
