<?php

namespace TOTS\LaravelCrudGenerator\Generators;

use TOTS\LaravelCrudGenerator\FileGenerator;

class ServiceGenerator extends FileGenerator
{
    protected array $serviceMethods;
    protected array $methodsContent;
    protected string $entityModel;
    protected string $entityVar;
    protected bool $staticMethods;

    public function setFileContent() : void
    {
        $this->setServiceMethods();
        $this->setEntityVar();
        $this->setEntityModel();
        $this->setMethods();
    }

    public function setServiceMethods() : void
    {
        $this->staticMethods = $this->fileData && property_exists( $this->fileData, 'static_methods' )? $this->fileData->static_methods : $this->configurationOptions[ $this->fileType ][ 'static_methods' ];
        $this->serviceMethods = $this->fileData && property_exists( $this->fileData, 'methods' )? $this->fileData->methods : $this->configurationOptions[ $this->fileType ][ 'methods' ];
        if( !in_array( 'fetch', $this->serviceMethods ) && ( in_array( 'update', $this->serviceMethods ) || in_array( 'delete', $this->serviceMethods ) ) ) $this->serviceMethods[] = 'fetch';
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

    public function setEntityModel() : void
    {
        $this->entityModel = $this->entityData->modelClassname;
        $this->fileUseUrls[] = $this->entityData->modelUrl;
    }

    public function setMethods() : void
    {
        foreach( $this->serviceMethods as $method )
        {
            $methodResponse = $this->entityModel;
            if( self::isCannonicalMethod( $method ) )
            {
                $methodToGenerateContent = 'generate' . $method . 'MethodContent';
                $methodContent = $this->$methodToGenerateContent();
                $methodToGenerateArguments = 'generate' . $method . 'MethodArguments';
                $methodArguments = method_exists( $this, $methodToGenerateArguments )? $this->$methodToGenerateArguments() : null;
                $methodToGenerateResponse = 'generate' . $method . 'MethodResponse';
                if( method_exists( $this, $methodToGenerateResponse ) )
                    $methodResponse = $this->$methodToGenerateResponse();
            }else{
                $methodContent = $this->generateDefaultMethodContent();
                $methodArguments = "Request \$request";
            }
            $methodBaseTemplate = parent::generateMethodTemplate( $method, $methodArguments, $methodResponse, $this->staticMethods );
            $this->methodsContent[ $method ] = str_replace( '{{ method_content }}', $methodContent, $methodBaseTemplate );
        }
    }

    public function generateDefaultMethodContent() : string
    {
        return "// TO DO";
    }

    public function generateStoreMethodContent() : string
    {
        return "return {$this->entityModel}::create( {$this->entityVar}Data );";
    }

    public function generateStoreMethodArguments() : string
    {
        return "array {$this->entityVar}Data";
    }

    public function generateUpdateMethodContent() : string
    {
        $self = $this->staticMethods? 'self::' : '$this->';
        return "{$this->entityVar} = {$self}fetch( {$this->entityVar}Id );
        {$this->entityVar}->update( {$this->entityVar}Data );
        return {$this->entityVar};";
    }

    public function generateUpdateMethodArguments() : string
    {
        return "array {$this->entityVar}Data, int {$this->entityVar}Id";
    }

    public function generateDeleteMethodContent() : string
    {
        $self = $this->staticMethods? 'self::' : '$this->';
        return "{$this->entityVar} = {$self}fetch( {$this->entityVar}Id );
        {$this->entityVar}->delete();
        return {$this->entityVar};";
    }

    public function generateDeleteMethodArguments() : string
    {
        return "int {$this->entityVar}Id";
    }

    public function generateFetchMethodContent() : string
    {
        return "return {$this->entityModel}::find( {$this->entityVar}Id );";
    }

    public function generateFetchMethodArguments() : string
    {
        return "int {$this->entityVar}Id";
    }

    public function generateListMethodContent() : string
    {
        return "return {$this->entityModel}::paginate( \$filters );";
    }

    public function generateListMethodArguments() : string
    {
        return "array \$filters = []";
    }

    public function generateListMethodResponse() : string
    {
        return "\\Illuminate\\Pagination\\LengthAwarePaginator";
    }
}
