<?php

namespace TOTS\LaravelCrudGenerator;

use Illuminate\Support\Facades\File;
use TOTS\LaravelCrudGenerator\Interfaces\FileGeneratorInterface;
use Illuminate\Support\Str;

abstract class FileGenerator implements FileGeneratorInterface
{
    protected string $entityName;
    protected object $entityData;
    protected string $filePath;
    protected string $fileName;
    protected string $fileUrl;
    protected string $fileContent;
    protected array $configurationOptions;
    protected string $generatorType;
    protected object $generatorData;
    protected string $classname;
    protected string $entitySingularName;
    protected string $entityPluralName;


    public function __construct( string $entityName, array $entityData, string $filePath = null )
    {
        $this->entityName = $entityName;
        $this->entityData = $entityData;
        $this->filePath = $filePath;
        $this->configurationOptions = require config_path( 'laravelCrudGenerator.php' );
        $this->setEntitySingularName();
        $this->setEntityPluralName();
        $this->setGeneratorType();
        $this->setFilePath();
        $this->makePathDirectory();
        $this->setFileName();
        $this->setFileUrl();
        $this->setClassNamespace();
        $this->initFileContentFromStub();
    }

    public function setEntitySingularName() : void
    {
        if( $this->entityData && $this->entityData->nameSingular ) $this->classname = ucfirst( $this->entityData->nameSingular );
    }

    public function setEntityPluralName() : void
    {

    }

    public function setGeneratorData()
    {
        $generatorType = $this->generatorType;
        $this->generatorData = $this->entityData->$generatorType;
    }

    public function setFilePath() : void
    {

        if( $this->generatorData && $this->generatorData->filePath ) $this->filePath = $this->generatorData->filePath;
        if( !$this->filePath ) $this->filePath = $this->configurationOptions[ $this->generatorType ][ 'file_path' ];
    }

    protected function makePathDirectory() : void
    {
        if( !File::exists( $this->filePath ) ) File::makeDirectory( $this->filePath );
    }

    public function setFileName()
    {
        if( !$this->fileName ) $this->fileName = $this->classname . '.php';
    }

    public function setFileUrl()
    {
        $this->fileUrl = $this->filePath . '/' . $this->fileName;
    }

    public function setClassname()
    {
        $classname = $this->generatorData && $this->generatorData->classname? $this->generatorData->classname : $this->entitySingularName;
        $this->classname = Str::camel( $classname );
    }

    public function generateFile() : void
    {
        $this->generateFileContent();
        $this->createFile();
    }


    public function createFile() : void
    {
        File::put( $this->fileUrl, $this->fileContent );
    }



    public function setClassNamespace()
    {

    }

}
