<?php

namespace TOTS\LaravelCrudGenerator\Generators;

use TOTS\LaravelCrudGenerator\FileGenerator;
use Illuminate\Support\Str;

class ResourceGenerator extends FileGenerator
{
    protected array $attributes = [];

    public function setFileContent() : void
    {
        $this->setAttributes();
    }

    public function setFilePath() : void
    {
        parent::setFilePath();
        $this->filePath .= "/" . Str::studly( $this->entityName );
    }

    public function setClassNamespace() : void
    {
        parent::setClassNamespace();
        $this->classNamespace = $this->classNamespace? $this->classNamespace . '\\' . Str::studly( $this->entityName ) : $this->classNamespace;
    }

    public function generateFileContent() : void
    {
        parent::generateFileContent();
        $attributes = implode( "", $this->attributes );
        $attributes = $attributes? $attributes . "\n\t\t" : "";
        $this->fileContent = str_replace( '{{ attributes }}', $attributes, $this->fileContent );
    }

    public function setAttributes() : void
    {
        if( !$this->entityData || !property_exists( $this->entityData, 'attributes' ) ) return;
        $id = $this->fileData && property_exists( $this->fileData, 'id' ) && $this->fileData->id !== false? $this->fileData->id : 'id';
        $this->attributes[ $id ] = "\n\t\t\t'$id' => \$this->resource->$id,";
        foreach( $this->entityData->attributes as $attributeName => $attributeData )
        {
            $this->attributes[ $attributeName ] = "\n\t\t\t'$attributeName' => \$this->resource->$attributeName,";
        }
    }

}
