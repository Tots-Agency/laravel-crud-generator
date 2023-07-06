<?php

namespace TOTS\LaravelCrudGenerator;

use Illuminate\Support\Facades\File;
use TOTS\LaravelCrudGenerator\Interfaces\FileGeneratorInterface;
use Illuminate\Support\Str;

abstract class FileGenerator implements FileGeneratorInterface
{
    protected string $entityName;
    protected string $entitySingularName;
    protected string $entityPluralName;
    protected object $entityData;
    protected string $filePath;
    protected string $fileName;
    protected string $fileUrl;
    protected string $fileContent;
    protected array $configurationOptions;
    protected string $generatorType;
    protected object $generatorData;
    protected string $classname;
    protected string $classNamespace;

    protected array $stubKeys = [ 'namespace', 'use', 'classname', 'extends', 'traits', 'implements' ];


    /**
     * Class constructor.
     *
     * @param string $entityName   The name of the entity.
     * @param object  $entityData   The data of the entity.
     * @param string $filePath     The file path (optional).
     */
    public function __construct( string $entityName, object $entityData )
    {
        $this->entityName = $entityName;
        $this->entityData = $entityData;
        $this->configurationOptions = require config_path( 'laravelCrudGenerator.php' );
        $this->setGeneratorType();
        $this->setGeneratorData();
        $this->setEntitySingularName();
        $this->setEntityPluralName();
        $this->setFilePath();
        $this->makePathDirectory();
        $this->setClassname();
        $this->setFileName();
        $this->setFileUrl();
        $this->setClassNamespace();
        $this->initFileContentFromStub();
    }

    /**
     * Set the value of the entity's singular name.
     *
     * @return void
     */
    public function setEntitySingularName() : void
    {
        $this->entitySingularName = strtolower( $this->entityData && $this->entityData->nameSingular? $this->entityData->nameSingular : $this->entityName );
    }

    /**
     * Set the value of the entity's plural name.
     *
     * @return void
     */
    public function setEntityPluralName() : void
    {
        $this->entityPluralName = strtolower( $this->entityData && $this->entityData->namePlural? $this->entityData->namePlural : Str::plural( $this->entitySingularName ) );
    }

    public function setGeneratorData() : void
    {
        $generatorType = $this->generatorType;
        $this->generatorData = $this->entityData->$generatorType;
    }

    public function setFilePath() : void
    {
        $this->filePath = $this->generatorData && $this->generatorData->filePath? $this->generatorData->filePath : $this->configurationOptions[ $this->generatorType ][ 'file_path' ];
    }

    protected function makePathDirectory() : void
    {
        if( !File::exists( $this->filePath ) ) File::makeDirectory( $this->filePath );
    }

    public function setFileName() : void
    {
        $this->fileName = $this->classname . '.php';
    }

    public function setFileUrl() : void
    {
        $this->fileUrl = $this->filePath . '/' . $this->fileName;
    }

    public function setClassname() : void
    {
        $this->classname = $this->generatorData && $this->generatorData->classname? $this->generatorData->classname : $this->entityName . ucfirst( $this->generatorType );
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

    public function setClassNamespace() : void
    {
        $this->classNamespace = $this->generatorData && $this->generatorData->namespace? $this->generatorData->namespace : $this->configurationOptions[ $this->generatorType ][ 'namespace' ];
    }

}
