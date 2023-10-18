<?php

namespace TOTS\LaravelCrudGenerator\Generators;

use Illuminate\Support\Facades\File;
use TOTS\LaravelCrudGenerator\FileGenerator;
use Illuminate\Support\Str;

class RoutesGenerator extends FileGenerator
{
    protected string $controller = '';
    protected string $controllerUrl = '';
    protected string $entity = '';
    protected string $entityPlural = '';
    protected array $controllerMethods = [];
    protected array $routes = [];

    public function setFileContent() : void
    {
        $this->getControllerMethods();
        $this->setRoutes();
    }

    public function getControllerMethods()
    {
        $this->controllerMethods = $this->entityData && property_exists( $this->entityData, 'controller' ) && property_exists( $this->entityData->controller, 'methods' )? $this->entityData->controller->methods : $this->configurationOptions[ 'controller' ][ 'methods' ];
    }

    public function setRoutes()
    {
        foreach( $this->controllerMethods as $methodName )
        {
            $generateRouteMethod = 'generate' . Str::studly( $methodName ) . 'Route';
            $this->routes[] = method_exists( $this, $generateRouteMethod )?
                $this->$generateRouteMethod() : $this->generateDefaultRoute( $methodName );
        }
    }

    public function generateRouteData( string $methodName ) : string
    {
        return "[ {$this->entityData->controllerClassname}::class, '{$methodName}' ] )->name( '{$this->entitySingularName}.{$methodName}' )->middleware( [ 'permission:{$this->entitySingularName}.{$methodName}' ]";
    }

    public function generateDefaultRoute( string $methodName ) : string
    {
        $routeData = $this->generateRouteData( $methodName );
        return "Route::post( '/$methodName', $routeData );";
    }

    public function generateListRoute() : string
    {
        $routeData = $this->generateRouteData( 'list' );
        return "Route::get( '/', $routeData );";
    }

    public function generateStoreRoute() : string
    {
        $routeData = $this->generateRouteData( 'store' );
        return "Route::post( '/', $routeData );";
    }

    public function generateShowRoute() : string
    {
        $routeData = $this->generateRouteData( 'show' );
        return "Route::get( '/{id}', $routeData );";
    }

    public function generateUpdateRoute() : string
    {
        $routeData = $this->generateRouteData( 'update' );
        return "Route::patch( '/{id}', $routeData );";
    }

    public function generateDeleteRoute() : string
    {
        $routeData = $this->generateRouteData( 'delete' );
        return "Route::delete( '/{id}', $routeData );";
    }

    public function generateRestoreRoute() : string
    {
        $routeData = $this->generateRouteData( 'restore' );
        return "Route::put( '/{id}', $routeData );";
    }

    public function generateFileContent() : void
    {
        $this->fileContent = File::get( __DIR__ . '/../Stubs/routes.stub' );
        $this->fileContent = str_replace( '{{ controller_url }}', $this->entityData->controllerUrl, $this->fileContent );
        $this->fileContent = str_replace( '{{ controller }}', $this->entityData->controllerClassname, $this->fileContent );
        $this->fileContent = str_replace( '{{ entity_plural }}', $this->entityPluralName, $this->fileContent );
        $this->fileContent = str_replace( '{{ routes }}', implode( "\n\t", $this->routes ), $this->fileContent );
    }
}
