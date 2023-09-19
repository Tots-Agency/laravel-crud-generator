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
    protected array $fileUseUrls;
    protected string $fileExtends;
    protected string $fileInterfaces;
    protected string $fileTraits;
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
        $this->setFileUseUrls();
        $this->setFileExtends();
        $this->setFileInterfaces();
        $this->setFileTraits();
        $this->setClassNamespace();
        $this->setFileContent();
    }

    public function setGeneratorType() : void
    {
        $this->fileType = strtolower( str_replace( [ 'TOTS\\LaravelCrudGenerator\\Generators\\', 'Generator' ], '', get_class( $this ) ) );
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
        $this->fileData = isset( $this->entityData ) && property_exists( $this->entityData, $fileType )? $this->entityData->$fileType : null;
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

    public function setFileUseUrls() : void
    {
        $this->fileUseUrls = [];
        if( ( $this->fileData && property_exists( $this->fileData, 'extends' ) && strpos( $this->fileData->extends, '\\') > -1 ) || ( isset( $this->configurationOptions[ $this->fileType ][ 'extends' ] ) && $this->configurationOptions[ $this->fileType ][ 'extends' ] && strpos( $this->configurationOptions[ $this->fileType ][ 'extends' ], '\\') > -1 ) )
            $this->fileUseUrls[] = $this->fileData && property_exists( $this->fileData, 'extends' )? $this->fileData->extends : $this->configurationOptions[ $this->fileType ][ 'extends' ];
        $this->fileUseUrls = array_merge( $this->fileUseUrls, $this->fileData && property_exists( $this->fileData, 'interfaces' )? $this->fileData->interfaces : $this->configurationOptions[ $this->fileType ][ 'interfaces' ] );
        $this->fileUseUrls = array_merge( $this->fileUseUrls, $this->fileData && property_exists( $this->fileData, 'traits' )? $this->fileData->traits : $this->configurationOptions[ $this->fileType ][ 'traits' ] );
        $this->fileUseUrls = array_merge( $this->fileUseUrls, $this->fileData && property_exists( $this->fileData, 'use' )? $this->fileData->use : $this->configurationOptions[ $this->fileType ][ 'use' ] );
    }

    public function addFileUseUrl( string $url ) : void
    {
        if( !in_array( $url, $this->fileUseUrls ) )
        {
            $this->fileUseUrls[] = $url;
        }
    }

    public function setFileExtends() : void
    {
        $this->fileExtends = '';
        if( ( $this->fileData && property_exists( $this->fileData, 'extends' ) ) || ( isset( $this->configurationOptions[ $this->fileType ][ 'extends' ] ) && $this->configurationOptions[ $this->fileType ][ 'extends' ] ) )
        {
            $extendUrl = $this->fileData && property_exists( $this->fileData, 'extends' )? $this->fileData->extends : $this->configurationOptions[ $this->fileType ][ 'extends' ];
            $this->fileExtends = ' extends ' . self::getClassNameFromUrl( $extendUrl );
        }
    }

    public function setFileInterfaces() : void
    {
        $this->fileInterfaces = '';
        if( $this->fileData && property_exists( $this->fileData, 'interfaces' ) || ( isset( $this->configurationOptions[ $this->fileType ][ 'interfaces' ] ) && $this->configurationOptions[ $this->fileType ][ 'interfaces' ] ) )
        {
            $interfaces = $this->fileData && property_exists( $this->fileData, 'interfaces' )? $this->fileData->interfaces : $this->configurationOptions[ $this->fileType ][ 'interfaces' ];
            foreach( $interfaces as $key => $interfaceUrl )
            {
                $interfaces[ $key ] = self::getClassNameFromUrl( $interfaceUrl );
            }
            $this->fileInterfaces = ' implements ' . implode( ', ', $interfaces );
        }
    }

    public function setFileTraits() : void
    {
        $this->fileTraits = '';
        if( $this->fileData && property_exists( $this->fileData, 'traits' ) || ( isset( $this->configurationOptions[ $this->fileType ][ 'traits' ] ) && $this->configurationOptions[ $this->fileType ][ 'traits' ] ) )
        {
            $traits = $this->fileData && property_exists( $this->fileData, 'traits' )? $this->fileData->traits : $this->configurationOptions[ $this->fileType ][ 'traits' ];
            foreach( $traits as $key => $traitsUrl )
            {
                $traits[ $key ] = self::getClassNameFromUrl( $traitsUrl );
            }
            $this->fileTraits = "\tuse " . implode( ', ', $traits ) . ";\n\n\t";
        }
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
        if( $this->fileType !== 'routes' )
            $this->classNamespace = $this->fileData && property_exists( $this->fileData, 'namespace' )? $this->fileData->namespace : $this->configurationOptions[ $this->fileType ][ 'namespace' ];
    }

    public function fileShouldBeCreated() : bool
    {
        $fileNotExist = !File::exists( $this->fileUrl );
        $fileCantBeRewrited = ( $this->fileData && property_exists( $this->fileData, 'rewrite' ) && $this->fileData->rewrite === false ) || ( !$this->fileData && $this->configurationOptions[ $this->fileType ][ 'rewrite' ] === false );
        $entityCantBeRewrited = ( $this->entityData && property_exists( $this->entityData, 'rewrite' ) && $this->entityData->rewrite === false ) || ( $this->entityData && $this->configurationOptions[ 'rewrite' ] === false );
        return $fileNotExist || !( $fileCantBeRewrited || $entityCantBeRewrited );
    }

    public static function getClassNameFromUrl( $classUrl ) : string
    {
        $className = explode( '\\', $classUrl );
        $className = $className[ count( $className ) - 1 ];
        $className = explode( ' as ', $className );
        return isset( $className[ 1 ] )? $className[ 1 ] : $className[ 0 ];
    }

    public static function isCannonicalMethod( string $method ) : bool
    {
        $methodToGenerateContent = 'generate' . $method . 'MethodContent';
        return method_exists( get_called_class(), $methodToGenerateContent );
    }

    public function generateFileContent() : void
    {
        $this->sanitizeUseUrls();
        $template = File::get( __DIR__ . '/Stubs/template.stub' );
        $this->fileContent = str_replace( '{{ file_stub }}', File::get( __DIR__ . "/Stubs/{$this->fileType}.stub" ), $template );
        $this->fileContent = str_replace( '{{ namespace }}', $this->classNamespace, $this->fileContent );
        $this->fileContent = str_replace( '{{ use }}', empty( $this->fileUseUrls )? '' : 'use ' . implode( ";\nuse ", $this->fileUseUrls ) . ";\n\n", $this->fileContent );
        $this->fileContent = str_replace( '{{ classname }}', $this->classname, $this->fileContent );
        $this->fileContent = str_replace( '{{ extends }}', $this->fileExtends, $this->fileContent );
        $this->fileContent = str_replace( '{{ interfaces }}', $this->fileInterfaces, $this->fileContent );
        $this->fileContent = str_replace( '{{ traits }}', $this->fileTraits, $this->fileContent );
    }

    public function sanitizeUseUrls()
    {
        foreach( $this->fileUseUrls as $key => $url )
        {
            $url = str_replace( $this->classNamespace . '\\', '', $url );
            if( strpos( $url, '\\' ) === false )
            {
                unset( $this->fileUseUrls[ $key ] );
            }
        }
        sort( $this->fileUseUrls );
    }

    public static function generateMethodTemplate( $methodName, $methodArguments = '', $methodReturnType = 'void', $methodIsStatic = false, $methodScope = 'public' ) : string
    {
        $methodData = [
            'method_name' => $methodName,
            'method_arguments' => $methodArguments,
            'method_return_type' => $methodReturnType,
            'method_static' => $methodIsStatic? ' static' : '',
            'method_scope' => $methodScope
        ];
        return self::generateFromTemplate( 'method', $methodData );
    }

    public static function generateFromTemplate( string $template, array $templateData ) : string
    {
        $template = File::get( __DIR__ . "/Stubs/$template.stub" );
        foreach( $templateData as $variable => $value )
        {
            $template = str_replace( "{{ $variable }}", $value, $template );
        }
        $template = str_replace( "\\t", "\t", $template );
        $template = str_replace( "\\n", "\n", $template );
        return $template;
    }

}
