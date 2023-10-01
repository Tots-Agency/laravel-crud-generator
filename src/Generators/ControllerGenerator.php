<?php

namespace TOTS\LaravelCrudGenerator\Generators;

use stdClass;
use Illuminate\Support\Str;
use TOTS\LaravelCrudGenerator\FileGenerator;

class ControllerGenerator extends FileGenerator
{
    protected array $controllerMethods;
    protected string $methodResponse;
    protected array $methodsContent;
    protected string $entityRepository;
    protected string $entityResource;
    protected string $entityCollection;
    protected string $entityVar;

    public function setFileContent() : void
    {
        $this->setControllerMethods();
        $this->setMethodsResponse();
        $this->setEntityRepository();
        $this->setEntityResource();
        $this->setEntityVar();
        $this->setMethods();
        $this->addResourceFilesToUseUrls();
    }

    public function setControllerMethods() : void
    {
        $this->controllerMethods = $this->fileData && property_exists( $this->fileData, 'methods' )? $this->fileData->methods : $this->configurationOptions[ $this->fileType ][ 'methods' ];
    }

    public function setMethodsResponse() : void
    {
        $this->methodResponse = $this->fileData && property_exists( $this->fileData, 'response' )? $this->fileData->response : $this->configurationOptions[ $this->fileType ][ 'response' ];
    }

    public function generateFileContent() : void
    {
        parent::generateFileContent();
        $this->fileContent = str_replace( '{{ methods }}', implode( "\n", $this->methodsContent ), $this->fileContent );
    }

    public function setEntityVar() : void
    {
        $this->entityVar = "\$" . strtolower( $this->entityName );
    }

    public function setEntityRepository() : void
    {
        $this->entityRepository = $this->entityData->repositoryClassname;
        $this->fileUseUrls[] = $this->entityData->repositoryUrl;
    }

    public function setEntityResource() : void
    {
        $this->entityResource = $this->entityName . 'Resource';
        $this->entityCollection = $this->entityName . 'Collection';
    }

    public function setMethods() : void
    {
        foreach( $this->controllerMethods as $method )
        {
            if( self::isCannonicalMethod( $method ) )
            {
                $methodToGenerateContent = 'generate' . $method . 'MethodContent';
                $methodContent = $this->$methodToGenerateContent();
                $methodToGenerateArguments = 'generate' . $method . 'MethodArguments';
                $methodArguments = method_exists( $this, $methodToGenerateArguments )? $this->$methodToGenerateArguments() : null;
            }else{
                $methodContent = $this->generateDefaultMethodContent();
                $methodArguments = $this->generateDefaultMethodArguments( $method ); //"Request \$request";
                if( !in_array( "Illuminate\\Http\\Request", $this->fileUseUrls ) ) $this->fileUseUrls[] = "Illuminate\\Http\\Request";
            }
            $methodBaseTemplate = parent::generateMethodTemplate( $method, $methodArguments, $this->methodResponse );
            $this->methodsContent[ $method ] = str_replace( '{{ method_content }}', $methodContent, $methodBaseTemplate );
        }
    }

    public function getMethodRequestFile( string $method ) : string
    {
        return Str::studly( $method ) . 'Request';
    }

    public function addRequestFileToUseUrls( string $requestFile ) : void
    {
        $this->fileUseUrls[] = $requestFile;
    }

    public function addResourceFilesToUseUrls() : void
    {
        $entityName = Str::studly( $this->entityName );
        $resourceNamespace = $this->entityData && property_exists( $this->entityData, 'resource' ) && property_exists( $this->entityData->resource, 'namespace' )? $this->entityData->resource->namespace : $this->configurationOptions[ 'resource' ][ 'namespace' ];
        $this->fileUseUrls[] = $resourceNamespace . "\\{$entityName}\\{$entityName}Resource";
        $this->fileUseUrls[] = $resourceNamespace . "\\{$entityName}\\{$entityName}Collection";
    }

    public function generateRequestFile( string $requestFile ) : string
    {
        $requestFile = Str::studly( $requestFile );
        $objectData = new stdClass();
        $objectData->files = [ 'request' ];
        $objectData->attributes = $this->entityData && property_exists( $this->entityData, 'attributes' )? $this->entityData->attributes : [];
        $requestDataExists = $this->entityData && property_exists( $this->entityData, 'request' );
        $objectData->request = $requestDataExists? $this->entityData->request : [];
        $objectData->request[ 'filePath' ] = $requestDataExists && property_exists( $this->entityData->request, 'filePath' )?
            $this->entityData->request->filePath:
            $this->configurationOptions[ 'request' ][ 'file_path' ];
        $objectData->request[ 'namespace' ] = $requestDataExists && property_exists( $this->entityData->request, 'namespace' )?
            $this->entityData->request->namespace:
            $this->configurationOptions[ 'request' ][ 'namespace' ];
        $objectData->request[ 'filePath' ] .= '/' . Str::studly( $this->entityName );
        $objectData->request[ 'namespace' ] .= '\\' . Str::studly( $this->entityName );
        $generator = new RequestGenerator( $requestFile, json_decode( json_encode( $objectData ) ) );
        $generator->createFile();
        return $objectData->request[ 'namespace' ] . '\\' . $this->getMethodRequestFile( $requestFile );
    }

    public function generateDefaultMethodContent() : string
    {
        return "// TO DO
        return response()->json( [
            'data' => []
        ] );";
    }

    public function generateDefaultMethodArguments( $method ) : string
    {
        $requestFile = $this->generateRequestFile( $method );
        $this->addRequestFileToUseUrls( $requestFile );
        return Str::afterLast( $requestFile, '\\' ) . " \$request";
    }

    public function generateStoreMethodContent() : string
    {
        return "{$this->entityVar} = {$this->entityRepository}::store( \$request->validated() );
        return response()->json( [
            'data' => {$this->entityResource}::make( {$this->entityVar} ),
        ], 201 );";
    }

    public function generateStoreMethodArguments() : string
    {
        $requestFile = $this->generateRequestFile( 'store' );
        $this->addRequestFileToUseUrls( $requestFile );
        return Str::afterLast( $requestFile, '\\' ) . " \$request";
    }

    public function generateUpdateMethodContent() : string
    {
        return "{$this->entityVar} = {$this->entityRepository}::update( \$request->validated(), {$this->entityVar}Id );
        return response()->json( [
            'data' => {$this->entityResource}::make( {$this->entityVar} ),
        ] );";
    }

    public function generateUpdateMethodArguments() : string
    {
        $requestFile = $this->generateRequestFile( 'update' );
        $this->addRequestFileToUseUrls( $requestFile );
        return Str::afterLast( $requestFile, '\\' ) . " \$request, int {$this->entityVar}Id";
    }

    public function generateDeleteMethodContent() : string
    {
        return "{$this->entityVar} = {$this->entityRepository}::delete( {$this->entityVar}Id );
        return {$this->entityVar} ?

        response()->json( [
            'data' => {$this->entityResource}::make( {$this->entityVar} ),
        ] ):

        response()->json( [
            'error' => '{$this->entityName} not found',
        ], 404 );";
    }

    public function generateDeleteMethodArguments() : string
    {
        return "int {$this->entityVar}Id";
    }

    public function generateFetchMethodContent() : string
    {
        return "{$this->entityVar} = {$this->entityRepository}::fetch( {$this->entityVar}Id );
        return {$this->entityVar} ?

        response()->json( [
            'data' => {$this->entityResource}::make( {$this->entityVar} ),
        ] ):

        response()->json( [
            'error' => '{$this->entityName} not found',
        ], 404 );";
    }

    public function generateFetchMethodArguments() : string
    {
        return "int {$this->entityVar}Id";
    }

    public function generateListMethodContent() : string
    {
        return "{$this->entityVar} = {$this->entityRepository}::list( \$request->validated() );
        return new {$this->entityCollection}( {$this->entityVar} );";
    }

    public function generateListMethodArguments() : string
    {
        $requestFile = $this->generateRequestFile( 'list' );
        $this->addRequestFileToUseUrls( $requestFile );
        return Str::afterLast( $requestFile, '\\' ) . " \$request";
    }
}
