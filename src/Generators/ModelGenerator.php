<?php

namespace TOTS\LaravelCrudGenerator\Generators;

use TOTS\LaravelCrudGenerator\FileGenerator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModelGenerator extends FileGenerator
{
    protected string $table = '';
    protected string $primaryKey = '';
    protected string $accessors = '';
    protected string $mutators = '';
    protected string $relations = '';

    public function setGeneratorType() : void
    {
        $this->fileType = 'model';
    }

    public function initFileContent() : void
    {
        $this->fileContent = File::get( __DIR__ . '/../Stubs/Model.stub' );
        $this->setTable();
        $this->setPrimaryKey();
    }

    public function setClassname() : void
    {
        $this->classname = $this->fileData && $this->fileData->classname? $this->fileData->classname : $this->entityName;
    }

    public function setTable() : void
    {
        if( $this->fileData && property_exists( $this->fileData, 'table' ) )
            $this->table = "protected \$table = '" . $this->fileData->table . "';\n\t";
    }

    public function setPrimaryKey() : void
    {
        if( $this->fileData && property_exists( $this->fileData, 'primaryKey' ) )
            $this->primaryKey = "protected \$primaryKey = '" . $this->fileData->primaryKey . "';\n\t";
    }

    public function generateFileContent() : void
    {
        parent::generateFileContent();
        $this->fileContent = str_replace( '{{ table }}', $this->table, $this->fileContent );
        $this->fileContent = str_replace( '{{ primary_key }}', $this->primaryKey, $this->fileContent );
        $this->fileContent = str_replace( '{{ accessors }}', $this->accessors, $this->fileContent );
        $this->fileContent = str_replace( '{{ mutators }}', $this->mutators, $this->fileContent );
        $this->fileContent = str_replace( '{{ relations }}', $this->relations, $this->fileContent );
    }


}
