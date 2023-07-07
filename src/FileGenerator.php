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
    protected $entityData;
    protected string $filePath;
    protected string $fileName;
    protected string $fileUrl;
    protected string $fileContent;
    protected array $configurationOptions;
    protected string $fileType;
    protected $fileData;
    protected string $classname;
    protected string $classNamespace;

    protected array $stubKeys = [ 'namespace', 'use', 'classname', 'extends', 'traits', 'implements' ];

    /**
     * Class constructor.
     *
     * @param string $entityName   The name of the entity.
     * @param $entityData   The data of the entity.
     */
    public function __construct( string $entityName, object $entityData = null )
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
        $this->entitySingularName = strtolower( $this->entityData && property_exists( $this->entityData, 'nameSingular' )? $this->entityData->nameSingular : $this->entityName );
    }

    /**
     * Set the value of the entity's plural name.
     *
     * @return void
     */
    public function setEntityPluralName() : void
    {
        $this->entityPluralName = strtolower( $this->entityData && property_exists( $this->entityData, 'namePlural' )? $this->entityData->namePlural : Str::plural( $this->entitySingularName ) );
    }

    public function setGeneratorData() : void
    {
        $fileType = $this->fileType;
        $this->fileData = $this->entityData? $this->entityData->$fileType : null;
    }

    public function setFilePath() : void
    {
        $this->filePath = $this->fileData && property_exists( $this->fileData, 'filePath' )? $this->fileData->filePath : $this->configurationOptions[ $this->fileType ][ 'file_path' ];
    }

    protected function makePathDirectory() : void
    {
        if( !File::exists( $this->filePath ) ) File::makeDirectory( $this->filePath, 0755, true );
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
        $this->classname = $this->fileData && property_exists( $this->fileData, 'classname' )? $this->fileData->classname : $this->entityName . ucfirst( $this->fileType );
    }

    public function generateFile() : void
    {
        $this->generateFileContent();
        $this->createFile();
    }

    public function createFile() : bool
    {
        if( $this->fileShouldBeCreated() )
        {
            $this->generateFileContent();
            File::put( $this->fileUrl, $this->fileContent );
            return true;
        }
        return false;
    }

    public function setClassNamespace() : void
    {
        $this->classNamespace = $this->fileData && property_exists( $this->fileData, 'namespace' )? $this->fileData->namespace : $this->configurationOptions[ $this->fileType ][ 'namespace' ];
    }

    public function fileShouldBeCreated() : bool
    {
        $fileNotExist = !File::exists( $this->fileUrl );
        $fileCantBeRewrited = ( $this->fileData && property_exists( $this->fileData, 'rewrite' ) && $this->fileData->rewrite === false ) || ( !$this->fileData && $this->configurationOptions[ $this->fileType ][ 'rewrite' ] === false );
        $entityCantBeRewrited = ( $this->entityData && property_exists( $this->entityData, 'rewrite' ) && $this->entityData->rewrite === false ) || ( $this->entityData && $this->configurationOptions[ 'rewrite' ] === false );
        return $fileNotExist || !( $fileCantBeRewrited || $entityCantBeRewrited );
    }

}
