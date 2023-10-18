<?php

namespace TOTS\LaravelCrudGenerator\Generators;

use TOTS\LaravelCrudGenerator\FileGenerator;
use Illuminate\Support\Str;

class FactoryGenerator extends FileGenerator
{
    protected string $attributes = '';

    public function setFileContent() : void
    {
        $this->addModelUrl();
        $this->setAttributes();
    }

    public function addModelUrl() : void
    {
        parent::addFileUseUrl( $this->entityData->modelUrl );
    }

    public function setAttributes() : void
    {
        $attributes = [];
        if( $this->entityData && property_exists( $this->entityData, 'attributes' ) )
        {
            foreach( $this->entityData->attributes as $attributeName => $attributeData )
            {
                $fakerType = $this->getFakerType( $attributeName, $attributeData );
                $attributes[] = "'{$attributeName}' => {$fakerType}";
            }
        }
        $this->attributes = empty( $attributes )? "// TO DO" : implode( ",\n\t\t\t", $attributes );
    }

    public function getFakerType( string $attributeName, object $attributeData ) : string
    {
        $integers = [ 'int', 'integer', 'bigInteger', 'unsignedBigInteger' ];
        $unique = property_exists( $attributeData, 'unique' ) && $attributeData->unique? '->unique()' : '';
        if( property_exists( $attributeData, 'type' ) )
        {
            if( $attributeData->type == 'string' && in_array( $attributeName, [ 'mail', 'email' ] ) )
                return "fake(){$unique}->email()";
            if( $attributeData->type == 'string' && in_array( $attributeName, [ 'name', 'fullname', 'full_name' ] ) )
                return "fake(){$unique}->firstName() . ' ' . fake(){$unique}->lastName()";
            if( $attributeData->type == 'string' && in_array( $attributeName, [ 'firstname', 'first_name' ] ) )
                return "fake(){$unique}->firstName()";
            if( $attributeData->type == 'string' && in_array( $attributeName, [ 'lastname', 'last_name' ] ) )
                return "fake(){$unique}->lastName()";
            if( $attributeData->type == 'string' && in_array( $attributeName, [ 'title' ] ) )
                return "fake(){$unique}->words( 5, true )";
            if( in_array( $attributeData->type, [ 'string', 'text' ] ) && in_array( $attributeName, [ 'desc', 'description', 'caption' ] ) )
                return "fake(){$unique}->text()";
            if( $attributeData->type == 'string' && in_array( $attributeName, [ 'address', 'fulladdress' ] ) )
                return "fake(){$unique}->address()";
            if( $attributeData->type == 'string' && in_array( $attributeName, [ 'postalcode', 'address_code' ] ) )
                return "fake(){$unique}->postcode()";
            if( $attributeData->type == 'date' ) return "fake(){$unique}->date()";
            if( $attributeData->type == 'datetime' ) return "fake(){$unique}->dateTime()";
            if( $attributeData->type == 'time' ) return "fake(){$unique}->time()";
            if( $attributeData->type == 'boolean' ) return "fake(){$unique}->boolean()";
            if( in_array( $attributeData->type, $integers ) ) return "fake(){$unique}->randomNumber()";
            if( in_array( $attributeData->type, [ 'float', 'decimal' ] ) ) return "fake(){$unique}->randomFloat( 2, 1, 100 )";
            if( str_contains( $attributeName, 'image' ) && in_array( $attributeData->type, [ 'string', 'text' ] ) )
                return "fake(){$unique}->imageUrl( 640, 480, 'people' )";
            if( str_contains( $attributeName, 'id' ) && in_array( $attributeData->type, $integers ) )
            {
                // TO DO
            }
        }
        return 'fake()->title()';
    }

    public function generateFileContent() : void
    {
        parent::generateFileContent();
        $this->fileContent = str_replace( '{{ attributes }}', $this->attributes, $this->fileContent );
        $this->fileContent = str_replace( '{{ model }}', $this->entityData->modelClassname, $this->fileContent );
        $this->fileContent = str_replace( '{{ var_name }}', Str::camel( $this->entityData->modelClassname ), $this->fileContent );
    }
}
