<?php

namespace TOTS\LaravelCrudGenerator;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use TOTS\LaravelCrudGenerator\Generators\ModelGenerator;

class LaravelCrudGenerator
{
    private $crudData;
    private $configurationOptions;

    public function __construct( $filePath = null )
    {
        $this->configurationOptions = require config_path( 'laravelCrudGenerator.php' );
        $this->crudData = json_decode( file_get_contents( $filePath?  $filePath : $this->configurationOptions[ 'default_file_path' ] ) );
    }

    public function generateFiles()
    {
        foreach( get_object_vars( $this->crudData->entities ) as $entityName => $entityData )
        {
            // dd( $entityName, '-----------------------------------', $entityData );
            $this->generateModel( $entityName, $entityData );
        }
    }

    public function generateModel( $entityName, $entityData )
    {
        $modelGenerator = new ModelGenerator( $entityName, $entityData );
        $modelGenerator->createFile();
    }

    // private function createModel()
    // {
    //     $modelStub = File::get( __DIR__ . '/Stubs/Model.stub' );
    //     $modelContent = str_replace( '{{entity}}', $this->entityName, $modelStub );

    //     $modelFolderPath = app_path( 'Models' );
    //     if( !File::exists( $modelFolderPath ) ) File::makeDirectory( $modelFolderPath );

    //     $modelPath = $modelFolderPath . '/' . $this->entityName . '.php';
    //     File::put( $modelPath, $modelContent );
    // }

    // private function createController()
    // {
    //     $controllerStub = File::get( __DIR__ . '/Stubs/Controller.stub' );
    //     $controllerContent = str_replace( '{{entity}}', $this->entityName, $controllerStub );

    //     $controllerFolderPath = app_path( 'Http/Controllers' );
    //     if( !File::exists( $controllerFolderPath ) ) File::makeDirectory( $controllerFolderPath );

    //     $controllerPath = $controllerFolderPath . '/' . $this->entityName . 'Controller.php';
    //     File::put( $controllerPath, $controllerContent );
    // }

    // private function createService()
    // {
    //     $serviceStub = File::get( __DIR__ . '/Stubs/Service.stub' );
    //     $serviceContent = str_replace( '{{entity}}', $this->entityName, $serviceStub );

    //     $serviceFolderPath = app_path( 'Services' );
    //     if( !File::exists( $serviceFolderPath ) ) File::makeDirectory( $serviceFolderPath );

    //     $servicePath = $serviceFolderPath . '/' . $this->entityName . 'Service.php';
    //     File::put( $servicePath, $serviceContent );
    // }

    // private function createRoutes()
    // {
    //     $routesStub = File::get(__DIR__ . '/Stubs/Routes.stub');
    //     $routesContent = str_replace('{{entity}}', $this->entityName, $routesStub);
    //     $routesPath = base_path('routes/' . strtolower($this->entityName) . '.php');
    //     File::put($routesPath, $routesContent);
    // }

    // private function createMigration()
    // {
    //     $testsStub = File::get( __DIR__ . '/Stubs/Migration.stub' );
    //     $testsContent = str_replace( '{{entity}}', $this->entityName, $testsStub );
    //     $migrationName = date( 'Y_m_d_His' ) . '_create_' . Str::snake( $this->entityName ) . 's_table';
    //     $testsPath = base_path( 'tests/Feature/' . $this->entityName . 'Test.php' );
    //     File::put( $testsPath, $testsContent );

    //     Artisan::call( 'make:migration create_' . strtolower( $this->entityName ) . 's_table --create=' . strtolower( $this->entityName ) );
    // }

    // private function createTest()
    // {
    //     $testsStub = File::get( __DIR__ . '/Stubs/Tests.stub' );
    //     $testsContent = str_replace( '{{entity}}', $this->entityName, $testsStub );
    //     $testsPath = base_path( 'tests/Feature/' . $this->entityName . 'Test.php' );
    //     File::put( $testsPath, $testsContent );
    // }

    // private function createFactory()
    // {
    //     $factoryStub = File::get( __DIR__ . '/Stubs/Factory.stub' );
    //     $factoryContent = str_replace( '{{entity}}', $this->entityName, $factoryStub );
    //     $factoryPath = base_path( 'database/factories/' . $this->entityName . 'Factory.php');
    //     File::put( $factoryPath, $factoryContent );
    // }

}
