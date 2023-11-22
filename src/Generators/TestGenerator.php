<?php

namespace TOTS\LaravelCrudGenerator\Generators;

use TOTS\LaravelCrudGenerator\FileGenerator;
use Illuminate\Foundation\Testing\WithFaker;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Str;

class TestGenerator extends FileGenerator
{
    use WithFaker;
    protected array $testingMethods;
    protected array $methodsContent;
    protected string $entityModel;
    protected string $entityVar;

    public function setFileContent() : void
    {
        $this->setTestingMethods();
        $this->setEntityVar();
        $this->setEntityModel();
        $this->setMethods();
    }

    public function setTestingMethods() : void
    {
        $this->testingMethods = $this->entityData && property_exists( $this->entityData, 'controller' ) && property_exists( $this->entityData->controller, 'methods' )? $this->entityData->controller->methods : $this->configurationOptions[ 'controller' ][ 'methods' ];
        if( !in_array( 'show', $this->testingMethods ) && ( in_array( 'update', $this->testingMethods ) || in_array( 'delete', $this->testingMethods ) ) ) $this->testingMethods[] = 'show';
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
        foreach( $this->testingMethods as $method )
        {
            if( self::isCannonicalMethod( $method ) )
            {
                $methodToGenerateContent = 'generate' . $method . 'MethodContent';
                $methodContent = $this->$methodToGenerateContent();
            }else{
                $methodContent = $this->generateDefaultMethodContent( $method );
            }
            $methodName = 'test_' . strtolower( $method ) . '_' . strtolower( $this->entityName );
            $methodBaseTemplate = parent::generateMethodTemplate( $methodName, '', null, false );
            $this->methodsContent[ $method ] = str_replace( '{{ method_content }}', $methodContent, $methodBaseTemplate );
        }
    }

    public function generateDefaultMethodContent( $method ) : string
    {
        $routeUrl = $this->getMethodRouteUrl( null, $method );
        return "// TO DO
        \$data = [];
        \$response = \$this->json('POST', \"$routeUrl\", \$data);
        \$response->assertStatus(200);";
    }

    public function generateStoreMethodContent() : string
    {
        $methodContent = "";
        $routeUrl = $this->getMethodRouteUrl();
        $routeMethod = $this->getMethodRouteMethod( 'store' );
        $tableName = $this->getTableName();
        $attributes = '';
        if( property_exists( $this->entityData, 'attributes' ) )
        {
            foreach( $this->entityData->attributes as $attribute => $data )
            {
                $value = $this->generateDataFromAttributeType( $data->type, $attribute );
                if( !is_numeric( $value ) ) $value = "'$value'";
                $attributes .= "\n\t\t\t'$attribute' => $value,";
            }
        }
        if( $attributes ) $methodContent .= "{$this->entityVar}Data = [$attributes\n\t\t];\n\t\t";

        $methodContent .= "\$response = \$this->json('$routeMethod', \"$routeUrl\", {$this->entityVar}Data);
        \$response->assertStatus(201);";
        if( $attributes ) $methodContent .= "\n\t\t\$this->assertDatabaseHas( '$tableName', {$this->entityVar}Data );";

        return $methodContent;
    }

    public function generateUpdateMethodContent() : string
    {
        $methodContent = "{$this->entityVar} = {$this->entityModel}::factory()->create();\n\t\t";
        $routeUrl = $this->getMethodRouteUrl( $this->entityVar );
        $routeMethod = $this->getMethodRouteMethod( 'update' );
        $tableName = $this->getTableName();
        $attributes = '';
        if( property_exists( $this->entityData, 'attributes' ) )
        {
            foreach( $this->entityData->attributes as $attribute => $data )
            {
                $value = $this->generateDataFromAttributeType( $data->type, $attribute );
                if( !is_numeric( $value ) ) $value = "'$value'";
                $attributes .= "\n\t\t\t'$attribute' => $value,";
            }
        }
        if( $attributes ) $methodContent .= "\$newData = [$attributes];\n\t\t";

        $methodContent .= "\$response = \$this->json('$routeMethod', \"$routeUrl\", \$newData);
        \$response->assertStatus(200);";
        if( $attributes ) $methodContent .= "\n\t\t\$this->assertDatabaseHas( '$tableName', \$newData );";
        return $methodContent;
    }

    public function generateDeleteMethodContent() : string
    {
        $methodContent = "{$this->entityVar} = {$this->entityModel}::factory()->create();\n\t\t";
        $routeUrl = $this->getMethodRouteUrl( $this->entityVar );
        $routeMethod = $this->getMethodRouteMethod( 'delete' );
        $tableName = $this->getTableName();

        $methodContent .= "\$response = \$this->json('$routeMethod', \"$routeUrl\");
        \$response->assertStatus(204);
        \$this->assertDatabaseMissing( '$tableName', ['id' => {$this->entityVar}->id] );";
        return $methodContent;
    }

    public function generateShowMethodContent() : string
    {
        $methodContent = "{$this->entityVar} = {$this->entityModel}::factory()->create();\n\t\t";
        $routeUrl = $this->getMethodRouteUrl( $this->entityVar );
        $routeMethod = $this->getMethodRouteMethod( 'show' );

        $methodContent .= "\$response = \$this->json('$routeMethod', \"$routeUrl\");
        \$response->assertStatus(200);";
        return $methodContent;
    }

    public function generateListMethodContent() : string
    {
        $methodContent = "{$this->entityVar} = {$this->entityModel}::factory(30)->create();\n\t\t";
        $routeUrl = $this->getMethodRouteUrl( $this->entityVar );
        $routeMethod = $this->getMethodRouteMethod( 'list' );

        $methodContent .= "\$response = \$this->json('$routeMethod', \"$routeUrl\");
        \$response->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => '*',
            ],
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'path',
                'per_page',
                'to',
                'total',
            ],
        ]);";
        return $methodContent;
    }

    public function generateDataFromAttributeType( $attributeType, $attributeName = null )
    {
        $this->faker = FakerFactory::create();
        if( in_array( $attributeName, [ 'name', 'first_name', 'firstname' ] )  ) return $this->faker()->firstName;
        if( in_array( $attributeName, [ 'last_name', 'lastname' ] )  ) return $this->faker()->lastName;
        if( in_array( $attributeName, [ 'mail', 'email', 'email_address', 'emailaddress' ] )  ) return $this->faker()->email;
        if( in_array( $attributeName, [ 'phone', 'cellphone', 'cell_phone', 'phone_number' ] )  ) return $this->faker()->phoneNumber();
        if( in_array( $attributeName, [ 'user', 'username', 'user_name', 'user_name' ] )  ) return $this->faker()->userName;
        if( in_array( $attributeName, [ 'pass', 'password', 'pswd' ] )  ) return $this->faker()->password();
        if( in_array( $attributeName, [ 'description', 'comment', 'caption', 'body' ] )  ) return $this->faker()->sentence();
        if( in_array( $attributeName, [ 'title' ] ) ) return $this->faker()->words(5,true);

        switch( $attributeType )
        {
            case 'string': return $this->faker()->word;
            case 'text': return $this->faker()->sentence;
            case 'integer': return $this->faker()->numberBetween( 1, 9999 );
            case 'bigInteger': return $this->faker()->numberBetween( 1, 99999999 );
            case 'decimal': case 'double': return $this->faker()->randomFloat( 2 );
            case 'boolean': return $this->faker()->boolean;
            case 'date': case 'dateTime': return $this->faker()->date();
        }
    }
    public function getMethodRouteUrl( $entityVar = null, $method = null )
    {
        $id =  $entityVar? '/{' . $entityVar . '->id}': '';
        $prefix = $method? '/' . $method : '';
        return '/' . $this->entityPluralName . $prefix . $id;
    }

    public function getMethodRouteMethod( $method )
    {
        switch( $method )
        {
            case 'store': return 'POST';
            case 'update': return 'PATCH';
            case 'delete': return 'DELETE';
            case 'restore': return 'PUT';
            default: return 'GET';
        }
    }

    public function getTableName()
    {
        return $this->entityData && property_exists( $this->entityData, 'migration' ) && property_exists( $this->entityData->migration, 'table' )? $this->entityData->migration->table : Str::snake( Str::plural( $this->entityName ) );
    }
}
